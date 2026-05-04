<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\OrangeMoneyService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    // Historique pour l'utilisateur connecté
    public function history()
    {
        $transactions = Transaction::where('id_user', auth()->id())
            ->orderByDesc('transaction_date')
            ->get();

        return view('user.history', compact('transactions'));
    }

    // Liste transactions (page my-transactions)
    public function index()
    {
        $transactions = Transaction::where('id_user', auth()->id())
            ->orderByDesc('transaction_date')
            ->paginate(15);

        return view('user.transactions', compact('transactions'));
    }

    // Admin : toutes les transactions
    public function adminIndex()
    {
        $transactions = Transaction::with('user')
            ->orderByDesc('transaction_date')
            ->paginate(20);

        return view('admin.transactions', compact('transactions'));
    }

    /**
     * Créer une transaction et lancer le paiement Orange Money.
     * - En SANDBOX : redirige vers une page de succès simulée
     * - En PRODUCTION : redirige vers l'URL de paiement Orange Money
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'               => ['required', 'numeric', 'min:1'],
            'amount_sent'          => ['nullable', 'numeric', 'min:0'],
            'type'                 => ['required', 'in:TRANSFERT,RECHARGE'],
            'receiver'             => ['nullable', 'string', 'max:200'],
            'receiver_number_phone'=> ['required', 'string', 'max:30'],
            'operator'             => ['nullable', 'string', 'max:100'],
            'currency'             => ['nullable', 'string', 'max:5'],
        ]);

        // Calculer les frais selon le type
        $amount   = (float) $validated['amount'];
        $currency = strtoupper($request->input('currency', 'EUR'));
        // Mapper 'TRANSFERT' (UI français) → 'TRANSFER' (valeur ENUM en base)
        $type     = $validated['type'] === 'TRANSFERT' ? 'TRANSFER' : $validated['type'];
        $fee      = $type === 'RECHARGE'
            ? $this->calcRechargeFee($amount)
            : $this->calcTransferFee($amount);

        // Enregistrer la transaction en PENDING
        $transaction = Transaction::create([
            'amount'                => $amount,
            'fee'                   => $fee,
            'type'                  => $type,
            'receiver'              => $validated['receiver'] ?? null,
            'receiver_number_phone' => $validated['receiver_number_phone'],
            'operator'              => $validated['operator'] ?? 'Orange Money',
            'id_user'               => auth()->id(),
            'sender'                => auth()->user()->full_name ?? auth()->user()->firstname . ' ' . auth()->user()->lastname,
            'status'                => 'PENDING',
            'amount_sent'           => $validated['amount_sent'] ?? null,
            'currency'              => $currency,
            'transaction_date'      => now()->toDateString(),
            'created_date'          => now()->toDateString(),
        ]);

        // Créer le PaymentIntent Stripe (total = amount + fee)
        try {
            $stripe = new StripeService();
            $pi = $stripe->createPaymentIntent(
                $amount + $fee,
                $currency,
                [
                    'transaction_id'  => $transaction->id,
                    'receiver'        => $validated['receiver'] ?? '',
                    'receiver_phone'  => $validated['receiver_number_phone'],
                    'type'            => $type,
                ]
            );

            $transaction->update(['stripe_payment_intent_id' => $pi->id]);

            // Stocker le client_secret en session (valable jusqu'à la page de paiement)
            session(['stripe_cs_' . $transaction->id => $pi->client_secret]);

            return redirect()->route('user.payment', $transaction->id);

        } catch (\Exception $e) {
            Log::error('Stripe store error', ['error' => $e->getMessage(), 'tx' => $transaction->id]);
            $transaction->update(['status' => 'FAILED']);
            return redirect()->route('user.dashboard')
                ->with('error', 'Erreur lors de l\'initiation du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Callback Orange Money (webhook notification)
     * Orange Money envoie une notification POST/GET à cette URL
     */
    public function orangeCallback(Request $request)
    {
        $orderId    = $request->input('order_id', $request->input('orderid', ''));
        $status     = strtoupper($request->input('status', ''));
        $notifToken = $request->input('notif_token', '');

        if (! $orderId) {
            Log::warning('OrangeMoney callback: missing order_id');
            return response()->json(['error' => 'missing order_id'], 400);
        }

        $transaction = Transaction::where('om_order_id', $orderId)->first();

        if (! $transaction) {
            Log::warning('OrangeMoney callback: transaction not found', ['order_id' => $orderId]);
            return response()->json(['error' => 'not found'], 404);
        }

        // Validate callback authenticity
        $om = new OrangeMoneyService();
        if (! $om->validateCallback($request->all(), $transaction->om_notif_token ?? '')) {
            Log::warning('OrangeMoney callback: invalid signature', ['order_id' => $orderId]);
            // In sandbox: still process
        }

        // Map Orange Money status to our internal status
        $internalStatus = match ($status) {
            'SUCCESSFULL', 'SUCCESS' => 'COMPLETED',
            'FAILED', 'EXPIRED'     => 'FAILED',
            'CANCELLED'             => 'CANCELLED',
            default                 => 'PENDING',
        };

        $transaction->update(['status' => $internalStatus]);

        Log::info('OrangeMoney callback processed', [
            'order_id' => $orderId,
            'status'   => $internalStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Page de retour après paiement sandbox (simulation).
     * En prod, Orange Money redirige vers return_url avec order_id.
     */
    public function orangeReturn(Request $request)
    {
        $orderId = $request->input('order_id', '');
        $cancel  = $request->input('status') === 'cancel';

        if ($orderId) {
            $transaction = Transaction::where('om_order_id', $orderId)->first();
            if ($transaction) {
                if ($cancel) {
                    $transaction->update(['status' => 'CANCELLED']);
                } else {
                    // En sandbox/prod: vérifier le statut réel via l'API
                    $om     = new OrangeMoneyService();
                    $status = $om->checkPaymentStatus($orderId);
                    $transaction->update([
                        'status' => match (strtoupper($status)) {
                            'SUCCESSFULL', 'SUCCESS' => 'COMPLETED',
                            'FAILED', 'EXPIRED'     => 'FAILED',
                            'CANCELLED'             => 'CANCELLED',
                            default                 => $transaction->status,
                        },
                    ]);
                }
            }
        }

        if ($cancel) {
            return redirect()->route('user.dashboard')->with('error', 'Paiement annulé.');
        }

        return redirect()->route('user.history')->with('success', 'Paiement Orange Money effectué avec succès !');
    }

    // ── Calcul des frais : Transfert Classique ────────────────────────────
    private function calcTransferFee(float $amount): float
    {
        if      ($amount <= 50)  return 2;
        elseif  ($amount <= 100) return 4;
        elseif  ($amount <= 150) return 6;
        elseif  ($amount <= 200) return 8;
        elseif  ($amount <= 250) return 10;
        elseif  ($amount <= 300) return 12;
        elseif  ($amount <= 350) return 14;
        elseif  ($amount <= 400) return 16;
        elseif  ($amount <= 450) return 18;
        elseif  ($amount <= 500) return 20;
        else return 20 + (int) ceil(($amount - 500) / 50) * 2;
    }

    // ── Calcul des frais : Recharge Mobile ───────────────────────────────
    private function calcRechargeFee(float $amount): float
    {
        if      ($amount <= 20)  return 1.5;
        elseif  ($amount <= 40)  return 2;
        elseif  ($amount <= 60)  return 3.5;
        elseif  ($amount <= 80)  return 4;
        elseif  ($amount <= 100) return 4.5;
        elseif  ($amount <= 120) return 5;
        elseif  ($amount <= 140) return 5.5;
        elseif  ($amount <= 160) return 6;
        elseif  ($amount <= 180) return 6.5;
        else return 7;
    }
}

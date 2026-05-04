<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\OrangeMoneyService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    /**
     * Afficher la page de paiement Stripe pour une transaction donnée.
     */
    public function showPayment(Request $request, int $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('id_user', auth()->id())
            ->where('status', 'PENDING')
            ->firstOrFail();

        $clientSecret = session('stripe_cs_' . $id);

        if (! $clientSecret) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Session de paiement expirée. Veuillez recommencer.');
        }

        $publishableKey = config('services.stripe.key');

        return view('user.payment', compact('transaction', 'clientSecret', 'publishableKey'));
    }

    /**
     * Gérer le retour de Stripe après confirmCardPayment (redirect_url).
     */
    public function return(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent');
        $redirectStatus  = $request->input('redirect_status');

        if (! $paymentIntentId) {
            return redirect()->route('user.dashboard')->with('error', 'Paramètre de retour Stripe manquant.');
        }

        $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntentId)
            ->where('id_user', auth()->id())
            ->first();

        if (! $transaction) {
            Log::warning('StripeReturn: transaction not found', ['pi' => $paymentIntentId]);
            return redirect()->route('user.dashboard')->with('error', 'Transaction introuvable.');
        }

        if ($redirectStatus !== 'succeeded') {
            $transaction->update(['status' => 'FAILED']);
            return redirect()->route('user.dashboard')->with('error', 'Paiement annulé ou échoué.');
        }

        // Vérifier le statut réel via l'API Stripe
        try {
            $stripe = new StripeService();
            $pi = $stripe->retrieve($paymentIntentId);

            if ($pi->status !== 'succeeded') {
                $transaction->update(['status' => 'FAILED']);
                return redirect()->route('user.dashboard')->with('error', 'Paiement non confirmé par Stripe.');
            }

            // Déclencher le payout Orange Money
            $this->processPayout($transaction);

        } catch (\Exception $e) {
            Log::error('StripeReturn error', ['error' => $e->getMessage(), 'tx' => $transaction->id]);
            return redirect()->route('user.history')
                ->with('success', 'Paiement reçu. Le transfert est en cours de traitement.');
        }

        // Effacer le client_secret de la session
        session()->forget('stripe_cs_' . $transaction->id);

        return redirect()->route('user.history')
            ->with('success', 'Paiement effectué ! Votre transfert est en cours.');
    }

    /**
     * Webhook Stripe — reçoit payment_intent.succeeded pour confirmation serveur.
     */
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $stripe = new StripeService();
            $event  = $stripe->constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook: invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi          = $event->data->object;
            $transaction = Transaction::where('stripe_payment_intent_id', $pi->id)->first();

            if ($transaction && $transaction->status === 'PENDING') {
                try {
                    $this->processPayout($transaction);
                } catch (\Exception $e) {
                    Log::error('Stripe webhook: payout error', ['error' => $e->getMessage(), 'tx' => $transaction->id]);
                    return response()->json(['error' => 'Payout failed'], 500);
                }
            }
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Déclencher le payout Orange Money et marquer la transaction COMPLETED.
     */
    private function processPayout(Transaction $transaction): void
    {
        // Calcul du montant GNF : utiliser amount_sent si disponible
        $amountGnf = $transaction->amount_sent ?? ($transaction->amount * 10000); // fallback approximatif

        $om        = new OrangeMoneyService();
        $reference = 'PAYOUT-' . $transaction->id . '-' . time();

        $result = $om->sendPayout(
            $amountGnf,
            $transaction->receiver_number_phone,
            $reference
        );

        Log::info('OrangeMoney payout sent', [
            'tx'     => $transaction->id,
            'result' => $result,
        ]);

        $transaction->update([
            'status'    => 'COMPLETED',
            'om_order_id' => $result['transaction_id'] ?? $reference,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PdfController extends Controller
{
    /** Aperçu de la facture dans le navigateur */
    public function preview(int $id): View
    {
        $transaction = Transaction::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        $user = auth()->user();

        return view('user.invoice-preview', compact('transaction', 'user'));
    }

    /** Téléchargement PDF de la facture */
    public function invoice(int $id): Response
    {
        $transaction = Transaction::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        $user = auth()->user();

        $html = view('pdf.invoice', compact('transaction', 'user'))->render();

        // Si barryvdh/laravel-dompdf est installé
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait');
            return $pdf->download('facture-MAMCash-' . str_pad($id, 6, '0', STR_PAD_LEFT) . '.pdf');
        }

        // Fallback HTML si dompdf non installé
        return response($html, 200)->header('Content-Type', 'text/html');
    }
}

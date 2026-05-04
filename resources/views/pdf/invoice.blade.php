<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  @page { size: A4 portrait; margin: 0 0 55px 0; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2d3748; background: #fff; }

  /* ── Header ── */
  .header { background: #1B365D; color: white; padding: 22px 30px; }
  .header-inner { display: table; width: 100%; }
  .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
  .brand { font-size: 24px; font-weight: bold; letter-spacing: 1px; color: #FFD700; }
  .brand-sub { font-size: 10px; color: #b8cfe8; margin-top: 2px; }
  .invoice-label { font-size: 10px; color: #b8cfe8; text-transform: uppercase; letter-spacing: 1px; }
  .invoice-num { font-size: 20px; font-weight: bold; color: #FFD700; margin-top: 2px; }
  .invoice-date { font-size: 10px; color: #b8cfe8; margin-top: 3px; }

  /* ── Status banner ── */
  .status-badge {
    display: inline-block; padding: 4px 14px; border-radius: 20px;
    font-size: 10px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase;
  }
  .status-completed { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .status-pending   { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
  .status-failed    { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  .status-other     { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

  /* ── Two-col info ── */
  .info-grid { display: table; width: 100%; margin-bottom: 16px; }
  .info-col  { display: table-cell; vertical-align: top; width: 48%; padding-right: 16px; }
  .info-col.right { padding-right: 0; padding-left: 16px; }
  .info-box { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 11px 14px; }
  .info-box-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #718096; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
  .info-row { margin-bottom: 4px; }
  .info-label { color: #718096; font-size: 10px; }
  .info-value { color: #2d3748; font-weight: bold; font-size: 10px; }

  /* ── Amounts table ── */
  .amounts-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #718096; margin-bottom: 8px; }
  .amounts-table { width: 100%; border-collapse: collapse; }
  .amounts-table td { padding: 8px 12px; border-bottom: 1px solid #edf2f7; font-size: 11px; }
  .amounts-table tr:last-child td { border-bottom: none; }
  .amounts-table .label { color: #718096; width: 55%; }
  .amounts-table .value { text-align: right; font-weight: bold; color: #2d3748; }
  .amounts-table tr.total-row { background: #ebf8ff; }
  .amounts-table tr.total-row td { font-size: 12px; font-weight: bold; color: #1B365D; border-top: 2px solid #bee3f8; }
  .amounts-table tr.gnf-row { background: #f0fff4; }
  .amounts-table tr.gnf-row td { color: #276749; border-top: 1px solid #c6f6d5; }

  /* ── Type badge ── */
  .type-transfer { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; border-radius: 3px; padding: 1px 7px; font-size: 9px; font-weight: bold; }
  .type-recharge { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 3px; padding: 1px 7px; font-size: 9px; font-weight: bold; }

  /* ── Footer : fixé en bas de page ── */
  .footer {
    position: fixed; bottom: -45px; left: 0; right: 0;
    background: #f7fafc; border-top: 2px solid #1B365D;
    padding: 10px 30px; height: 50px;
  }
  .footer-inner { display: table; width: 100%; }
  .footer-left  { display: table-cell; vertical-align: middle; font-size: 9px; color: #718096; }
  .footer-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 9px; color: #718096; }
  .footer-brand { font-weight: bold; color: #1B365D; font-size: 10px; }

  /* ── Watermark for PENDING ── */
  .watermark { position: fixed; top: 38%; left: 15%; font-size: 72px; font-weight: bold; color: rgba(255,193,7,0.10); transform: rotate(-35deg); letter-spacing: 8px; text-transform: uppercase; }
</style>
</head>
<body>
@php
  $devise      = strtoupper($transaction->currency ?? 'EUR');
  $isRecharge  = $transaction->type === 'RECHARGE';
  $amount      = (float) ($transaction->amount ?? 0);
  $fee         = (float) ($transaction->fee ?? 0);
  $total       = $amount + $fee;
  $amountSent  = $transaction->amount_sent ?? null;
  $status      = $transaction->status ?? 'N/A';
  $statusClass = match($status) {
    'SUCCESS', 'COMPLETED' => 'status-completed',
    'PENDING'              => 'status-pending',
    'FAILED'               => 'status-failed',
    default                => 'status-other',
  };
  $statusLabel = match($status) {
    'COMPLETED' => 'Complété',
    'SUCCESS'   => 'Succès',
    'PENDING'   => 'En attente',
    'FAILED'    => 'Échoué',
    default     => $status,
  };
  $txDate = $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : date('d/m/Y');
@endphp

{{-- Watermark for pending --}}
@if($status === 'PENDING')
<div class="watermark">En attente</div>
@endif

{{-- ── HEADER ── --}}
<div class="header">
  <div class="header-inner">
    <div class="header-left">
      <div class="brand">MAMCash</div>
      <div class="brand-sub">Transfert d'argent vers l'Afrique de l'Ouest</div>
    </div>
    <div class="header-right">
      <div class="invoice-label">Facture</div>
      <div class="invoice-num">N° {{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
      <div class="invoice-date">Date : {{ $txDate }}</div>
      <div class="invoice-date" style="margin-top:4px">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>
  </div>
</div>

{{-- ── STATUS BAR ── --}}
<div style="background:#fff;padding:10px 30px;border-bottom:1px solid #e2e8f0;">
  <table width="100%"><tr>
    <td style="font-size:11px;color:#718096;">
      Expéditeur : <strong style="color:#2d3748;">{{ $user->firstname }} {{ $user->lastname }}</strong>
      &nbsp;·&nbsp; {{ $user->email }}
    </td>
    <td style="text-align:right;">
      <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
    </td>
  </tr></table>
</div>

{{-- ── BODY ── --}}
<div style="padding: 18px 30px;">

  {{-- Two-col: Expéditeur + Bénéficiaire --}}
  <div class="info-grid">
    <div class="info-col">
      <div class="info-box">
        <div class="info-box-title">&#128100; Expéditeur</div>
        <div class="info-row"><span class="info-label">Nom :</span> <span class="info-value">{{ $user->firstname }} {{ $user->lastname }}</span></div>
        <div class="info-row"><span class="info-label">Email :</span> <span class="info-value">{{ $user->email }}</span></div>
        @if($user->phone ?? null)
        <div class="info-row"><span class="info-label">Téléphone :</span> <span class="info-value">{{ $user->phone }}</span></div>
        @endif
        <div class="info-row"><span class="info-label">Pays :</span> <span class="info-value">{{ $user->country ?? '—' }}</span></div>
      </div>
    </div>
    <div class="info-col right">
      <div class="info-box">
        <div class="info-box-title">&#127968; Bénéficiaire</div>
        <div class="info-row"><span class="info-label">Nom :</span> <span class="info-value">{{ $transaction->receiver ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Téléphone :</span> <span class="info-value">{{ $transaction->receiver_number_phone ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Opérateur :</span> <span class="info-value">{{ $transaction->operator ?? '—' }}</span></div>
        <div class="info-row" style="margin-top:8px">
          <span class="{{ $isRecharge ? 'type-recharge' : 'type-transfer' }}">
            {{ $isRecharge ? 'Recharge Mobile' : 'Transfert classique' }}
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- Amounts --}}
  <div class="amounts-title">&#128176; Détail du paiement</div>
  <div style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
    <table class="amounts-table">
      <tr>
        <td class="label">Montant envoyé</td>
        <td class="value">{{ number_format($amount, 2, ',', ' ') }} {{ $devise }}</td>
      </tr>
      <tr>
        <td class="label">Frais de service</td>
        <td class="value">{{ $fee > 0 ? number_format($fee, 2, ',', ' ') . ' ' . $devise : 'Offerts' }}</td>
      </tr>
      <tr class="total-row">
        <td class="label">&#9654; Montant total débité</td>
        <td class="value">{{ number_format($total, 2, ',', ' ') }} {{ $devise }}</td>
      </tr>
      @if(!$isRecharge && $amountSent)
      <tr class="gnf-row">
        <td class="label">&#10003; Montant reçu par le bénéficiaire</td>
        <td class="value">{{ number_format($amountSent, 0, ',', ' ') }} GNF</td>
      </tr>
      @endif
    </table>
  </div>

  {{-- Note si PENDING --}}
  @if($status === 'PENDING')
  <div style="margin-top:14px;background:#fffbeb;border:1px solid #f6e05e;border-radius:5px;padding:9px 12px;font-size:10px;color:#744210;">
    <strong>Transaction en cours de traitement.</strong> Vous recevrez une confirmation dès validation du paiement.
  </div>
  @endif

</div>

{{-- ── FOOTER (fixé en bas, hors flux) ── --}}
<div class="footer">
  <div class="footer-inner">
    <div class="footer-left">
      <div class="footer-brand">MAMCash</div>
      <div>Transfert d'argent sécurisé vers l'Afrique de l'Ouest &nbsp;·&nbsp; Ce document est une preuve de transaction officielle.</div>
    </div>
    <div class="footer-right">
      <div>Réf. : <strong>#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
      @if($transaction->stripe_payment_intent_id)
      <div style="margin-top:2px;font-size:8px;">Stripe : {{ $transaction->stripe_payment_intent_id }}</div>
      @endif
      <div style="margin-top:3px;color:#a0aec0;">www.MAMCash.com</div>
    </div>
  </div>
</div>

</body>
</html>

@extends('layouts.app')
@section('title', 'Facture N° ' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT))

@push('styles')
<style>
  .invoice-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 24px rgba(27,54,93,0.12);
    overflow: hidden;
    max-width: 780px;
    margin: 0 auto;
  }
  .inv-header {
    background: linear-gradient(135deg, #1B365D 0%, #2c4a6b 100%);
    color: white;
    padding: 28px 36px;
  }
  .inv-header-grid { display: flex; justify-content: space-between; align-items: center; }
  .inv-brand { font-size: 26px; font-weight: bold; color: #FFD700; letter-spacing: 1px; }
  .inv-brand-sub { font-size: 11px; color: #b8cfe8; margin-top: 3px; }
  .inv-num-label { font-size: 11px; color: #b8cfe8; text-align: right; text-transform: uppercase; letter-spacing: 1px; }
  .inv-num { font-size: 20px; font-weight: bold; color: #FFD700; text-align: right; margin-top: 3px; }
  .inv-date { font-size: 11px; color: #b8cfe8; text-align: right; margin-top: 4px; }

  .inv-status-bar {
    padding: 10px 36px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #718096;
  }
  .inv-status-bar strong { color: #2d3748; }

  .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
  .s-completed { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .s-pending   { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
  .s-failed    { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  .s-other     { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

  .inv-body { padding: 28px 36px; }

  .inv-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
  .inv-box { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px; }
  .inv-box-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #718096; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; }
  .inv-info-row { margin-bottom: 6px; font-size: 12px; }
  .inv-info-row .lbl { color: #718096; }
  .inv-info-row .val { font-weight: 600; color: #2d3748; }

  .type-transfer { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; border-radius: 4px; padding: 2px 10px; font-size: 11px; font-weight: bold; }
  .type-recharge { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 4px; padding: 2px 10px; font-size: 11px; font-weight: bold; }

  .inv-amounts-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #718096; margin-bottom: 10px; }
  .inv-amounts-table { width: 100%; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; border-collapse: collapse; }
  .inv-amounts-table td { padding: 10px 16px; border-bottom: 1px solid #edf2f7; font-size: 13px; }
  .inv-amounts-table tr:last-child td { border-bottom: none; }
  .inv-amounts-table .lbl { color: #718096; width: 55%; }
  .inv-amounts-table .val { text-align: right; font-weight: 600; color: #2d3748; }
  .inv-amounts-table .row-total { background: #ebf8ff; }
  .inv-amounts-table .row-total td { font-size: 14px; font-weight: bold; color: #1B365D; border-top: 2px solid #bee3f8; }
  .inv-amounts-table .row-gnf { background: #f0fff4; }
  .inv-amounts-table .row-gnf td { color: #276749; }

  .inv-pending-note { margin-top: 18px; background: #fffbeb; border: 1px solid #f6e05e; border-radius: 6px; padding: 10px 14px; font-size: 12px; color: #744210; }

  .inv-footer { background: #f7fafc; border-top: 2px solid #1B365D; padding: 16px 36px; display: flex; justify-content: space-between; align-items: center; }
  .inv-footer .brand-f { font-weight: bold; color: #1B365D; font-size: 12px; }
  .inv-footer .sub-f { font-size: 11px; color: #718096; margin-top: 2px; }
  .inv-footer .ref-f { text-align: right; font-size: 11px; color: #718096; }
  .inv-footer .ref-f strong { color: #1B365D; }
</style>
@endpush

@section('content')
@php
  $devise     = strtoupper($transaction->currency ?? 'EUR');
  $isRecharge = $transaction->type === 'RECHARGE';
  $amount     = (float) ($transaction->amount ?? 0);
  $fee        = (float) ($transaction->fee ?? 0);
  $total      = $amount + $fee;
  $amountSent = $transaction->amount_sent ?? null;
  $status     = $transaction->status ?? 'N/A';
  $sClass     = match($status) {
    'SUCCESS', 'COMPLETED' => 's-completed',
    'PENDING'              => 's-pending',
    'FAILED'               => 's-failed',
    default                => 's-other',
  };
  $sLabel = match($status) {
    'COMPLETED' => 'Complété', 'SUCCESS' => 'Succès',
    'PENDING'   => 'En attente', 'FAILED' => 'Échoué',
    default => $status,
  };
  $txDate = $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : date('d/m/Y');
  $invoiceNum = str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
@endphp

<div class="row justify-content-center my-3">
  <div class="col-xl-9 col-lg-10">

    {{-- Barre d'actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <a href="{{ route('user.history') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour à l'historique
      </a>
      <a href="{{ route('user.history.pdf', $transaction->id) }}" class="btn btn-success btn-sm px-4">
        <i class="fas fa-file-download me-2"></i>Télécharger en PDF
      </a>
    </div>

    {{-- Facture --}}
    <div class="invoice-card">

      {{-- Header --}}
      <div class="inv-header">
        <div class="inv-header-grid">
          <div>
            <div class="inv-brand">MAMCash</div>
            <div class="inv-brand-sub">Transfert d'argent vers l'Afrique de l'Ouest</div>
          </div>
          <div>
            <div class="inv-num-label">Facture</div>
            <div class="inv-num">N° {{ $invoiceNum }}</div>
            <div class="inv-date">Date : {{ $txDate }}</div>
            <div class="inv-date">Généré le {{ date('d/m/Y à H:i') }}</div>
          </div>
        </div>
      </div>

      {{-- Status bar --}}
      <div class="inv-status-bar">
        <span>Expéditeur : <strong>{{ $user->firstname }} {{ $user->lastname }}</strong> &nbsp;·&nbsp; {{ $user->email }}</span>
        <span class="status-badge {{ $sClass }}">{{ $sLabel }}</span>
      </div>

      {{-- Body --}}
      <div class="inv-body">

        {{-- Deux colonnes --}}
        <div class="inv-two-col">
          <div class="inv-box">
            <div class="inv-box-title"><i class="fas fa-user me-1"></i> Expéditeur</div>
            <div class="inv-info-row"><span class="lbl">Nom :</span> <span class="val">{{ $user->firstname }} {{ $user->lastname }}</span></div>
            <div class="inv-info-row"><span class="lbl">Email :</span> <span class="val">{{ $user->email }}</span></div>
            @if($user->phone ?? null)
            <div class="inv-info-row"><span class="lbl">Téléphone :</span> <span class="val">{{ $user->phone }}</span></div>
            @endif
            <div class="inv-info-row"><span class="lbl">Pays :</span> <span class="val">{{ $user->country ?? '—' }}</span></div>
          </div>
          <div class="inv-box">
            <div class="inv-box-title"><i class="fas fa-user-check me-1"></i> Bénéficiaire</div>
            <div class="inv-info-row"><span class="lbl">Nom :</span> <span class="val">{{ $transaction->receiver ?? '—' }}</span></div>
            <div class="inv-info-row"><span class="lbl">Téléphone :</span> <span class="val">{{ $transaction->receiver_number_phone ?? '—' }}</span></div>
            <div class="inv-info-row"><span class="lbl">Opérateur :</span> <span class="val">{{ $transaction->operator ?? '—' }}</span></div>
            <div class="inv-info-row mt-2">
              <span class="{{ $isRecharge ? 'type-recharge' : 'type-transfer' }}">
                <i class="fas fa-{{ $isRecharge ? 'mobile-alt' : 'exchange-alt' }} me-1"></i>
                {{ $isRecharge ? 'Recharge Mobile' : 'Transfert classique' }}
              </span>
            </div>
          </div>
        </div>

        {{-- Montants --}}
        <div class="inv-amounts-title"><i class="fas fa-coins me-1"></i> Détail du paiement</div>
        <table class="inv-amounts-table">
          <tr>
            <td class="lbl">Montant envoyé</td>
            <td class="val">{{ number_format($amount, 2, ',', ' ') }} {{ $devise }}</td>
          </tr>
          <tr>
            <td class="lbl">Frais de service</td>
            <td class="val">{{ $fee > 0 ? number_format($fee, 2, ',', ' ') . ' ' . $devise : '<span class="text-success">Offerts</span>' }}</td>
          </tr>
          <tr class="row-total">
            <td class="lbl"><i class="fas fa-chevron-right me-1"></i><strong>Montant total débité</strong></td>
            <td class="val">{{ number_format($total, 2, ',', ' ') }} {{ $devise }}</td>
          </tr>
          @if(!$isRecharge && $amountSent)
          <tr class="row-gnf">
            <td class="lbl"><i class="fas fa-check-circle me-1"></i> Montant reçu par le bénéficiaire</td>
            <td class="val">{{ number_format($amountSent, 0, ',', ' ') }} GNF</td>
          </tr>
          @endif
        </table>

        {{-- Note PENDING --}}
        @if($status === 'PENDING')
        <div class="inv-pending-note mt-3">
          <i class="fas fa-hourglass-half me-2"></i>
          <strong>Transaction en cours de traitement.</strong> Vous recevrez une confirmation dès validation du paiement.
        </div>
        @endif

      </div>

      {{-- Footer --}}
      <div class="inv-footer">
        <div>
          <div class="brand-f">MAMCash</div>
          <div class="sub-f">Transfert d'argent sécurisé vers l'Afrique de l'Ouest</div>
          <div class="sub-f">Ce document est une preuve de transaction officielle.</div>
        </div>
        <div class="ref-f">
          <div>Réf. : <strong>#{{ $invoiceNum }}</strong></div>
          @if($transaction->stripe_payment_intent_id)
          <div class="mt-1" style="font-size:10px;color:#a0aec0;">{{ $transaction->stripe_payment_intent_id }}</div>
          @endif
          <div class="mt-1" style="color:#a0aec0;">www.MAMCash.com</div>
        </div>
      </div>

    </div>{{-- /invoice-card --}}

    {{-- Bouton download bas de page --}}
    <div class="text-center mt-4 mb-3">
      <a href="{{ route('user.history.pdf', $transaction->id) }}" class="btn btn-success px-5 py-2">
        <i class="fas fa-file-download me-2"></i>Télécharger la facture PDF
      </a>
    </div>

  </div>
</div>
@endsection

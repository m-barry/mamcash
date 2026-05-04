@extends('layouts.app')
@section('title', 'Historique des virements')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-11 col-lg-12">
    <h3 class="mb-3"><i class="fas fa-history me-2"></i>Historique de mes virements</h3>

    <div class="mb-3">
      <a class="btn btn-outline-primary" href="{{ route('user.dashboard') }}">
        <i class="fas fa-arrow-left me-1"></i>Retour au tableau de bord
      </a>
    </div>

    @if($transactions->isEmpty())
      <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>Aucun virement trouvé.
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-striped table-hover table-sm mt-3 align-middle">
          <thead class="table-primary">
            <tr>
              <th>Date</th>
              <th>Bénéficiaire</th>
              <th>Montant envoyé</th>
              <th>Frais</th>
              <th>Montant total</th>
              <th>Reçu (GNF)</th>
              <th>Statut</th>
              <th>Opérateur</th>
              <th>Facture</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions as $t)
            @php
              $isRecharge  = $t->type === 'RECHARGE';
              $devise      = $isRecharge ? 'GNF' : strtoupper($t->currency ?? 'EUR');
              $fee         = (float) ($t->fee ?? 0);
              $amount      = (float) ($t->amount ?? 0);
              $total       = $amount + $fee;
              $badgeClass  = match($t->status ?? '') {
                'SUCCESS', 'COMPLETED' => 'bg-success',
                'PENDING'              => 'bg-warning text-dark',
                'FAILED'               => 'bg-danger',
                default                => 'bg-secondary',
              };
            @endphp
            <tr>
              <td class="text-nowrap">{{ $t->transaction_date ? $t->transaction_date->format('d/m/Y') : '—' }}</td>
              <td>{{ $t->receiver ?? $t->destination_iban ?? '—' }}</td>
              <td class="text-nowrap">{{ number_format($amount, 2, ',', ' ') }} {{ $devise }}</td>
              <td class="text-nowrap">{{ $fee > 0 ? number_format($fee, 2, ',', ' ') . ' ' . $devise : '—' }}</td>
              <td class="text-nowrap fw-semibold">{{ number_format($total, 2, ',', ' ') }} {{ $devise }}</td>
              <td class="text-nowrap">
                @if(!$isRecharge && $t->amount_sent)
                  {{ number_format($t->amount_sent, 0, ',', ' ') }} GNF
                @else
                  —
                @endif
              </td>
              <td><span class="badge {{ $badgeClass }}">{{ $t->status ?? 'N/A' }}</span></td>
              <td>{{ $t->operator ?? '—' }}</td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('user.history.preview', $t->id) }}" class="btn btn-outline-primary btn-sm" title="Consulter la facture">
                    <i class="fas fa-eye me-1"></i>Voir
                  </a>
                  <a href="{{ route('user.history.pdf', $t->id) }}" class="btn btn-success btn-sm" title="Télécharger la facture PDF">
                    <i class="fas fa-file-download me-1"></i>PDF
                  </a>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection

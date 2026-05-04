@extends('layouts.admin')
@section('title', 'Toutes les transactions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="fas fa-exchange-alt me-2"></i>Toutes les transactions</h2>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
    <i class="fas fa-arrow-left me-1"></i>Tableau de bord
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-primary">
          <tr>
            <th>#</th><th>Date</th><th>Expéditeur</th><th>Bénéficiaire</th>
            <th>Montant envoyé</th><th>Frais</th><th>Montant total</th>
            <th>Type</th><th>Statut</th><th>Opérateur</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactions as $t)
          <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->transaction_date ? $t->transaction_date->format('d/m/Y') : '—' }}</td>
            <td>{{ $t->user?->firstname }} {{ $t->user?->lastname }}</td>
            <td>{{ $t->receiver ?? $t->destination_iban ?? '—' }}</td>
            <td>{{ number_format($t->amount, 2, ',', ' ') }} €</td>
            <td>{{ number_format($t->fee ?? 0, 2, ',', ' ') }} €</td>
            <td class="fw-bold">{{ number_format(($t->amount ?? 0) + ($t->fee ?? 0), 2, ',', ' ') }} €</td>
            <td><span class="badge bg-secondary">{{ $t->type }}</span></td>
            <td>
              @php
                $bc = match($t->status ?? '') {
                  'SUCCESS','COMPLETED' => 'bg-success',
                  'PENDING' => 'bg-warning text-dark',
                  'FAILED'  => 'bg-danger',
                  default   => 'bg-secondary',
                };
              @endphp
              <span class="badge {{ $bc }}">{{ $t->status ?? 'N/A' }}</span>
            </td>
            <td>{{ $t->operator ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">{{ $transactions->links() }}</div>
</div>
@endsection

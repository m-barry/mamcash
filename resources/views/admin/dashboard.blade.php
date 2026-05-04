@extends('layouts.admin')
@section('title', 'Tableau de bord administrateur')

@push('styles')
<style>
  .stat-card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .stat-card .card-body { padding: 1.5rem; }
  .stat-label { font-size: .85rem; color: #6c757d; margin-bottom: .4rem; }
  .stat-value { font-size: 2rem; font-weight: 700; margin: 0; }
  .chart-card  { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
</style>
@endpush

@section('content')
<h1 class="text-center mb-1">Tableau de bord administrateur</h1>
<p class="text-center text-muted mb-4">Gérez les utilisateurs, les transactions et consultez les statistiques mensuelles.</p>

{{-- Statistiques --}}
<div class="row text-center mb-4">
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card stat-card">
      <div class="card-body">
        <p class="stat-label">Utilisateurs actifs</p>
        <p class="stat-value text-primary">{{ $activeUsers }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card stat-card">
      <div class="card-body">
        <p class="stat-label">Utilisateurs inactifs</p>
        <p class="stat-value text-secondary">{{ $inactiveUsers }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card stat-card">
      <div class="card-body">
        <p class="stat-label">Transactions totales</p>
        <p class="stat-value text-success">{{ $totalTransactions }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card stat-card">
      <div class="card-body">
        <p class="stat-label">Montant total</p>
        <p class="stat-value" style="color:#FFD700">€{{ number_format($totalAmount, 2) }}</p>
      </div>
    </div>
  </div>
</div>

{{-- Graphiques --}}
<div class="row mb-4">
  <div class="col-md-8 mb-3">
    <div class="card chart-card h-100">
      <div class="card-body">
        <h6 class="text-center text-muted mb-3">Transactions mensuelles</h6>
        <canvas id="chartMonthly" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card chart-card h-100">
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <h6 class="text-center text-muted mb-3">Répartition des utilisateurs</h6>
        <canvas id="chartUsers" height="180"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- Sélecteur utilisateur --}}
<div class="card shadow-sm mb-4">
  <div class="card-header text-white" style="background:#4a5568">
    <i class="fas fa-users me-2"></i>Liste des utilisateurs inscrits
  </div>
  <div class="card-body">
    <label class="form-label fw-semibold">Sélectionner un utilisateur</label>
    <select class="form-select" id="userSelect" onchange="if(this.value) window.location='/admin/customers/'+this.value+'/edit'">
      <option value="">-- Choisir un utilisateur --</option>
      @foreach($allUsers as $u)
      <option value="{{ $u->id }}">{{ $u->firstname }} {{ $u->lastname }} — {{ $u->email }}</option>
      @endforeach
    </select>
  </div>
  <div class="card-footer">
    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">
      <i class="fas fa-users me-1"></i>Voir tous les utilisateurs
    </a>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Graphique barres — transactions mensuelles
new Chart(document.getElementById('chartMonthly'), {
  type: 'bar',
  data: {
    labels: @json($monthLabels),
    datasets: [{
      label: 'Transactions',
      data: @json($monthCounts),
      backgroundColor: 'rgba(100,160,220,0.7)',
      borderRadius: 4,
    }]
  },
  options: {
    plugins: { legend: { display: true, position: 'top' } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});

// Graphique barres — répartition utilisateurs
new Chart(document.getElementById('chartUsers'), {
  type: 'bar',
  data: {
    labels: ['Actifs', 'Inactifs'],
    datasets: [{
      data: [{{ $activeUsers }}, {{ $inactiveUsers }}],
      backgroundColor: ['rgba(72,199,142,0.7)', 'rgba(255,99,132,0.7)'],
      borderRadius: 4,
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});
</script>
@endpush


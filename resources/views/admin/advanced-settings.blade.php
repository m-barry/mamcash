@extends('layouts.admin')
@section('title', 'Paramètres avancés')

@push('styles')
<style>
  .adv-tab-nav .nav-link { color: #1B365D; font-weight: 600; border-radius: 0; border-bottom: 3px solid transparent; }
  .adv-tab-nav .nav-link.active { color: #1B365D; border-bottom: 3px solid #FFD700; background: transparent; }
  .adv-tab-nav .nav-link:hover { background: #f0f4fa; }
  .section-title { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #718096; margin-bottom: .5rem; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Paramètres avancés</h4>
  <a href="{{ route('admin.settings') }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-arrow-left me-1"></i>Retour aux paramètres
  </a>
</div>

@foreach(['success_rates','success_operators','success_fees','success_maintenance'] as $sk)
  @if(session($sk))
    <div class="alert alert-success alert-dismissible fade show">{{ session($sk) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
@endforeach
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ONGLETS INTERNES --}}
<ul class="nav adv-tab-nav border-bottom mb-4" id="advTabs" role="tablist">
  <li class="nav-item">
    <button class="nav-link active px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabRates" type="button">
      <i class="fas fa-exchange-alt me-1"></i>Taux de change
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabOperators" type="button">
      <i class="fas fa-mobile-alt me-1"></i>Opérateurs
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabFees" type="button">
      <i class="fas fa-percent me-1"></i>Frais
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabMaintenance" type="button">
      <i class="fas fa-wrench me-1"></i>Maintenance
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabStats" type="button">
      <i class="fas fa-chart-bar me-1"></i>Statistiques
    </button>
  </li>
</ul>

<div class="tab-content">

  {{-- ─── TAUX DE CHANGE ─── --}}
  <div class="tab-pane fade show active" id="tabRates">
    <div class="card">
      <div class="card-header text-white" style="background:#0c6374;">
        <i class="fas fa-exchange-alt me-2"></i>Taux de change (vers GNF)
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.rates.update') }}">
          @csrf
          <div class="row g-4">
            <div class="col-md-4">
              <label class="form-label fw-bold">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" width="22" class="me-1"> EUR → GNF
              </label>
              <div class="input-group">
                <input type="number" name="rate_EUR" class="form-control" value="{{ $rates['EUR'] }}" step="1" min="1" required>
                <span class="input-group-text">GNF</span>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">
                <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" width="22" class="me-1"> USD → GNF
              </label>
              <div class="input-group">
                <input type="number" name="rate_USD" class="form-control" value="{{ $rates['USD'] }}" step="1" min="1" required>
                <span class="input-group-text">GNF</span>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg" width="22" class="me-1"> CAD → GNF
              </label>
              <div class="input-group">
                <input type="number" name="rate_CAD" class="form-control" value="{{ $rates['CAD'] }}" step="1" min="1" required>
                <span class="input-group-text">GNF</span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-info text-white mt-4">
            <i class="fas fa-save me-1"></i>Enregistrer les taux
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ─── OPÉRATEURS ─── --}}
  <div class="tab-pane fade" id="tabOperators">
    <div class="card">
      <div class="card-header bg-warning">
        <i class="fas fa-mobile-alt me-2"></i>Opérateurs Mobile Money
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.operator.add') }}" class="row g-2 mb-4">
          @csrf
          <div class="col-md-3">
            <input type="text" name="name" class="form-control" placeholder="Nom opérateur" required>
          </div>
          <div class="col-md-2">
            <select name="country" class="form-select" required>
              <option value="GN">Guinée</option>
              <option value="SN">Sénégal</option>
              <option value="CI">Côte d'Ivoire</option>
              <option value="ML">Mali</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="text" name="code_prefix" class="form-control" placeholder="Préfixe">
          </div>
          <div class="col-md-3">
            <input type="url" name="logo_url" class="form-control" placeholder="URL logo">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-warning w-100">Ajouter</button>
          </div>
        </form>

        @if($operators->isNotEmpty())
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
              <tr><th>Nom</th><th>Pays</th><th>Préfixe</th><th>Statut</th><th>Visibilité (inactif)</th><th>Actions</th></tr>
            </thead>
            <tbody>
              @foreach($operators as $op)
              <tr>
                <td><strong>{{ $op->name }}</strong></td>
                <td>{{ $op->country }}</td>
                <td><code>{{ $op->code_prefix ?: '-' }}</code></td>
                <td>
                  <span class="badge {{ $op->active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $op->active ? 'Actif' : 'Inactif' }}
                  </span>
                </td>
                <td>
                  @if(!$op->active)
                    <form method="POST" action="{{ route('admin.settings.operator.coming-soon', $op->id) }}" class="d-inline">
                      @csrf @method('PATCH')
                      <button class="btn btn-sm {{ $op->show_coming_soon ? 'btn-info' : 'btn-outline-secondary' }}"
                              title="{{ $op->show_coming_soon ? 'Affiché avec badge Bientôt — cliquer pour masquer' : 'Masqué — cliquer pour afficher avec badge Bientôt' }}">
                        @if($op->show_coming_soon)
                          <i class="fas fa-eye me-1"></i>Affiché « Bientôt »
                        @else
                          <i class="fas fa-eye-slash me-1"></i>Masqué
                        @endif
                      </button>
                    </form>
                  @else
                    <span class="text-muted small">—</span>
                  @endif
                </td>
                <td>
                  <form method="POST" action="{{ route('admin.settings.operator.toggle', $op->id) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm {{ $op->active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                      {{ $op->active ? 'Désactiver' : 'Activer' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.settings.operator.delete', $op->id) }}" class="d-inline"
                        onsubmit="return confirm('Supprimer cet opérateur ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
          <p class="text-muted">Aucun opérateur enregistré.</p>
        @endif
      </div>
    </div>
  </div>

  {{-- ─── FRAIS ─── --}}
  <div class="tab-pane fade" id="tabFees">
    <div class="card">
      <div class="card-header bg-danger text-white">
        <i class="fas fa-percent me-2"></i>Frais de transfert classique
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.fees.update') }}" id="feesForm">
          @csrf
          <div class="table-responsive mb-3">
            <table class="table table-bordered" id="feeTiersTable">
              <thead class="table-light">
                <tr><th>Montant &lt;= (EUR)</th><th>Frais (EUR)</th><th></th></tr>
              </thead>
              <tbody id="feeTiersBody">
                @php
                  $tierKeys = array_filter(array_keys($feeTiers), fn($k) => is_numeric($k));
                  sort($tierKeys);
                @endphp
                @foreach($tierKeys as $limit)
                <tr>
                  <td><input type="number" name="tiers[{{ $loop->index }}][limit]" class="form-control" value="{{ $limit }}" min="1" required></td>
                  <td><input type="number" name="tiers[{{ $loop->index }}][fee]" class="form-control" value="{{ $feeTiers[$limit] }}" step="0.5" min="0" required></td>
                  <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeTier(this)"><i class="fas fa-trash"></i></button></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm mb-4" onclick="addTier()">
            <i class="fas fa-plus me-1"></i>Ajouter un palier
          </button>
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label">Frais de base</label>
              <input type="number" name="above_base" class="form-control" value="{{ $feeTiers['above_base'] ?? 20 }}" step="0.5" min="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Par tranche de</label>
              <input type="number" name="above_step" class="form-control" value="{{ $feeTiers['above_step'] ?? 50 }}" min="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Incrément</label>
              <input type="number" name="above_increment" class="form-control" value="{{ $feeTiers['above_increment'] ?? 2 }}" step="0.5" min="0" required>
            </div>
          </div>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-save me-1"></i>Enregistrer les frais
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ─── MAINTENANCE ─── --}}
  <div class="tab-pane fade" id="tabMaintenance">
    <div class="card">
      <div class="card-header bg-dark text-white">
        <i class="fas fa-wrench me-2"></i>Mode maintenance
      </div>
      <div class="card-body text-center py-5">
        @if($maintenanceMode)
          <div class="display-3 text-warning mb-3"><i class="fas fa-exclamation-triangle"></i></div>
          <h4 class="text-warning fw-bold mb-1">Maintenance ACTIVÉE</h4>
          <p class="text-muted mb-4">Le service est actuellement indisponible pour les utilisateurs.</p>
        @else
          <div class="display-3 text-success mb-3"><i class="fas fa-check-circle"></i></div>
          <h4 class="text-success fw-bold mb-1">Service opérationnel</h4>
          <p class="text-muted mb-4">Tout fonctionne normalement.</p>
        @endif
        <form method="POST" action="{{ route('admin.settings.maintenance.toggle') }}">
          @csrf @method('PATCH')
          <button type="submit"
                  class="btn btn-lg {{ $maintenanceMode ? 'btn-success' : 'btn-warning' }}"
                  onclick="return confirm('Confirmer le changement de mode maintenance ?')">
            <i class="fas {{ $maintenanceMode ? 'fa-play' : 'fa-pause' }} me-2"></i>
            {{ $maintenanceMode ? 'Réactiver le service' : 'Activer la maintenance' }}
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ─── STATISTIQUES ─── --}}
  <div class="tab-pane fade" id="tabStats">

    {{-- Cartes principales --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
          <div class="card-body py-4">
            <div class="display-6 text-primary mb-1"><i class="fas fa-exchange-alt"></i></div>
            <p class="text-muted mb-1 small">Transactions totales</p>
            <h3 class="fw-bold text-primary">{{ number_format($stats['total_transactions']) }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
          <div class="card-body py-4">
            <div class="display-6 text-success mb-1"><i class="fas fa-euro-sign"></i></div>
            <p class="text-muted mb-1 small">Volume total</p>
            <h3 class="fw-bold text-success">{{ number_format($stats['total_volume'], 2) }} EUR</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
          <div class="card-body py-4">
            <div class="display-6 text-warning mb-1"><i class="fas fa-hand-holding-usd"></i></div>
            <p class="text-muted mb-1 small">Frais collectés</p>
            <h3 class="fw-bold text-warning">{{ number_format($stats['total_fees'], 2) }} EUR</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
          <div class="card-body py-4">
            <div class="display-6 text-info mb-1"><i class="fas fa-users"></i></div>
            <p class="text-muted mb-1 small">Utilisateurs</p>
            <h3 class="fw-bold text-info">{{ number_format($stats['total_users']) }}</h3>
          </div>
        </div>
      </div>
    </div>

    {{-- Ce mois-ci --}}
    <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:.75rem;letter-spacing:1px;">
      <i class="fas fa-calendar me-1"></i>Ce mois-ci — {{ now()->translatedFormat('F Y') }}
    </h6>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Transactions du mois</p>
            <h4 class="fw-bold">{{ number_format($stats['transactions_month']) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Volume du mois</p>
            <h4 class="fw-bold">{{ number_format($stats['volume_month'], 2) }} EUR</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Frais du mois</p>
            <h4 class="fw-bold">{{ number_format($stats['fees_month'], 2) }} EUR</h4>
          </div>
        </div>
      </div>
    </div>

    {{-- Répartition --}}
    <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:.75rem;letter-spacing:1px;">
      <i class="fas fa-pie-chart me-1"></i>Répartition des transactions
    </h6>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Transferts classiques</p>
            <h4 class="fw-bold text-primary">{{ number_format($stats['transfers_count']) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Recharges mobiles</p>
            <h4 class="fw-bold text-warning">{{ number_format($stats['recharge_count']) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="text-muted small mb-1">Utilisateurs actifs</p>
            <h4 class="fw-bold text-success">{{ number_format($stats['active_users']) }}</h4>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /tabStats --}}

</div>{{-- /tab-content --}}

@endsection

@push('scripts')
<script>
let tierIndex = {{ count(array_filter(array_keys($feeTiers), fn($k) => is_numeric($k))) }};
function addTier() {
  const tbody = document.getElementById('feeTiersBody');
  const row = document.createElement('tr');
  row.innerHTML = `<td><input type="number" name="tiers[${tierIndex}][limit]" class="form-control" min="1" required></td><td><input type="number" name="tiers[${tierIndex}][fee]" class="form-control" step="0.5" min="0" required></td><td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeTier(this)"><i class="fas fa-trash"></i></button></td>`;
  tbody.appendChild(row);
  tierIndex++;
}
function removeTier(btn) { btn.closest('tr').remove(); }

// Persistance de l'onglet actif via URL hash
const tabLinks = document.querySelectorAll('#advTabs [data-bs-toggle="tab"]');
tabLinks.forEach(tab => {
  tab.addEventListener('shown.bs.tab', () => history.replaceState(null, '', '#' + tab.dataset.bsTarget.replace('#', '')));
});
const hash = location.hash;
if (hash) {
  const el = document.querySelector('#advTabs [data-bs-target="' + hash + '"]');
  if (el) bootstrap.Tab.getOrCreateInstance(el).show();
}
</script>
@endpush

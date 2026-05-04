@extends('layouts.admin')
@section('title', 'Paramètres administrateur')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Paramètres administrateur</h4>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-arrow-left me-1"></i>Retour au tableau de bord
  </a>
</div>

@foreach(['success_profile','success_promo_transfer','success_promo_recharge','success_banner','success_rates','success_operators','success_fees','success_maintenance'] as $sk)
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

{{-- PROFIL ADMIN --}}
<div class="card mb-3">
  <div class="card-header text-white" style="background:#1B365D">
    <i class="fas fa-user me-2"></i>Profil administrateur
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.profile.update') }}">
      @csrf @method('PUT')
      <input type="hidden" name="lastname" value="{{ $admin->lastname }}">
      <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror"
               value="{{ old('firstname', $admin->firstname) }}" placeholder="Nom de l'administrateur" required>
        @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $admin->email) }}" placeholder="Email" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
  </div>
</div>

<button type="button" class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#modalPassword">
  <i class="fas fa-key me-2"></i>Changer le mot de passe
</button>
<p class="text-muted small mb-4">Pour renforcer la securite de votre compte, pensez a changer regulierement votre mot de passe.</p>

<div class="mb-4">
  <h6 class="mb-3"><i class="fas fa-sliders-h me-2"></i>Preferences</h6>
  <div class="d-flex align-items-center mb-2">
    <div class="form-check form-switch me-2">
      <input class="form-check-input" type="checkbox" id="prefDarkMode" style="width:2.5rem;height:1.25rem">
    </div>
    <label for="prefDarkMode">Activer le mode sombre</label>
  </div>
  <div class="d-flex align-items-center">
    <div class="form-check form-switch me-2">
      <input class="form-check-input" type="checkbox" id="prefNotifications" checked style="width:2.5rem;height:1.25rem">
    </div>
    <label for="prefNotifications">Recevoir les notifications</label>
  </div>
</div>

{{-- PROMOTIONS TRANSFERT --}}
<div class="card mb-3">
  <div class="card-header text-white" style="background:#1565C0">
    <i class="fas fa-percent me-2"></i>Promotions sur les frais de transfert
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.promotion.add') }}" class="row g-2 mb-3" id="formPromoTransfer">
      @csrf
      <input type="hidden" name="type" value="TRANSFER">
      <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="Code" required></div>
      <div class="col-md-4"><input type="text" name="description" class="form-control" placeholder="Description"></div>
      <div class="col-md-2"><input type="number" name="discount" class="form-control" placeholder="0" min="1" max="100" required></div>
      <div class="col-md-2 d-flex align-items-center">
        <div class="form-check"><input class="form-check-input" type="checkbox" name="active" id="activeT" value="1" checked><label class="form-check-label" for="activeT">Active</label></div>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Ajouter</button></div>
    </form>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <form method="POST" action="{{ route('admin.settings.promotions.delete-all', 'TRANSFER') }}" onsubmit="return confirm('Supprimer TOUTES les promotions de transfert ?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">Tout supprimer</button>
      </form>
      <button type="button" class="btn btn-sm btn-danger" onclick="filterPromos('transfer')">Cible</button>
      <button type="button" class="btn btn-sm btn-warning" onclick="document.getElementById('formPromoTransfer').reset()">REINITIALISER</button>
      <button type="button" class="btn btn-sm btn-info text-white" onclick="alert('{{ $promotionsTransfer->count() }} promotion(s), {{ $promotionsTransfer->where(`active`,true)->count() }} active(s).')">DIAGNOSTIC</button>
      <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Action NUCLEAIRE : supprimer toutes les promos de transfert ?')) document.querySelector('#deleteAllTransfer').submit()">NUCLEAIRE</button>
    </div>
    @if($promotionsTransfer->isEmpty())
      <p class="text-muted">Aucune promotion de transfert.</p>
    @else
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light"><tr><th>Code</th><th>Description</th><th>Reduction (%)</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
          @foreach($promotionsTransfer as $promo)
          <tr class="promo-row-transfer {{ $promo->active ? 'active-promo' : 'inactive-promo' }}">
            <td><code>{{ $promo->code }}</code></td>
            <td>{{ $promo->description }}</td>
            <td><strong>{{ $promo->discount }}</strong></td>
            <td><span class="badge {{ $promo->active ? 'bg-success' : 'bg-secondary' }}">{{ $promo->active ? 'Oui' : 'Non' }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.settings.promotion.toggle', $promo->id) }}" class="d-inline">@csrf @method('PATCH')
                <button class="btn btn-sm {{ $promo->active ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $promo->active ? 'Desactiver' : 'Activer' }}</button>
              </form>
              <form method="POST" action="{{ route('admin.settings.promotion.delete', $promo->id) }}" class="d-inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
    <small class="text-muted mt-2 d-block">Les promotions s'appliquent uniquement sur les <strong>frais de transfert</strong> et non sur le montant envoye.</small>
  </div>
</div>

{{-- PROMOTIONS RECHARGE --}}
<div class="card mb-3">
  <div class="card-header text-white" style="background:#1565C0">
    <i class="fas fa-percent me-2"></i>Promotions sur les frais de recharge mobile
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.promotion.add') }}" class="row g-2 mb-3" id="formPromoRecharge">
      @csrf
      <input type="hidden" name="type" value="RECHARGE">
      <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="Code" required></div>
      <div class="col-md-4"><input type="text" name="description" class="form-control" placeholder="Description"></div>
      <div class="col-md-2"><input type="number" name="discount" class="form-control" placeholder="0" min="1" max="100" required></div>
      <div class="col-md-2 d-flex align-items-center">
        <div class="form-check"><input class="form-check-input" type="checkbox" name="active" id="activeR" value="1" checked><label class="form-check-label" for="activeR">Active</label></div>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Ajouter</button></div>
    </form>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <form method="POST" action="{{ route('admin.settings.promotions.delete-all', 'RECHARGE') }}" onsubmit="return confirm('Supprimer TOUTES les promotions de recharge ?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">Tout supprimer</button>
      </form>
      <button type="button" class="btn btn-sm btn-danger" onclick="filterPromos('recharge')">Cible</button>
      <button type="button" class="btn btn-sm btn-warning" onclick="document.getElementById('formPromoRecharge').reset()">REINITIALISER</button>
      <button type="button" class="btn btn-sm btn-info text-white" onclick="alert('{{ $promotionsRecharge->count() }} promotion(s), {{ $promotionsRecharge->where(`active`,true)->count() }} active(s).')">DIAGNOSTIC</button>
      <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Action NUCLEAIRE : supprimer toutes les promos de recharge ?')) document.querySelector('#deleteAllRecharge').submit()">NUCLEAIRE</button>
    </div>
    @if($promotionsRecharge->isEmpty())
      <p class="text-muted">Aucune promotion de recharge.</p>
    @else
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light"><tr><th>Code</th><th>Description</th><th>Reduction (%)</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
          @foreach($promotionsRecharge as $promo)
          <tr class="promo-row-recharge {{ $promo->active ? 'active-promo' : 'inactive-promo' }}">
            <td><code>{{ $promo->code }}</code></td>
            <td>{{ $promo->description }}</td>
            <td><strong>{{ $promo->discount }}</strong></td>
            <td><span class="badge {{ $promo->active ? 'bg-success' : 'bg-secondary' }}">{{ $promo->active ? 'Oui' : 'Non' }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.settings.promotion.toggle', $promo->id) }}" class="d-inline">@csrf @method('PATCH')
                <button class="btn btn-sm {{ $promo->active ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $promo->active ? 'Desactiver' : 'Activer' }}</button>
              </form>
              <form method="POST" action="{{ route('admin.settings.promotion.delete', $promo->id) }}" class="d-inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
    <small class="text-muted mt-2 d-block">Les promotions s'appliquent uniquement sur les <strong>frais de recharge mobile</strong> et non sur le montant envoye.</small>
  </div>
</div>

{{-- BANNIERE PUBLICITAIRE --}}
<div class="card mb-4">
  <div class="card-header text-white" style="background:#1565C0">
    <i class="fas fa-bullhorn me-2"></i>Banniere publicitaire (Dashboard utilisateur)
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.banner.save') }}">
      @csrf
      <div class="row g-2 mb-3">
        <div class="col-md-4"><input type="text" name="banner_text" class="form-control" value="{{ old('banner_text', $banner['text']) }}" placeholder="Texte de la banniere"></div>
        <div class="col-md-4"><input type="text" name="banner_image_url" class="form-control" value="{{ old('banner_image_url', $banner['image_url']) }}" placeholder="https://... (image optionnelle)"></div>
        <div class="col-md-4"><input type="text" name="banner_link" class="form-control" value="{{ old('banner_link', $banner['link']) }}" placeholder="Lien (optionnel)"></div>
      </div>
      <div class="row g-2 align-items-end mb-3">
        <div class="col-auto">
          <label class="form-label mb-1">Du</label>
          <input type="date" name="banner_date_from" class="form-control" value="{{ old('banner_date_from', $banner['date_from']) }}">
        </div>
        <div class="col-auto">
          <label class="form-label mb-1">Au</label>
          <input type="date" name="banner_date_to" class="form-control" value="{{ old('banner_date_to', $banner['date_to']) }}">
        </div>
        <div class="col-auto">
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="banner_active" id="bannerActive" value="1" {{ $banner['active'] ? 'checked' : '' }}>
            <label class="form-check-label" for="bannerActive">Active</label>
          </div>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </form>
    @if($banner['text'])
    <div class="mt-2">
      <strong>Apercu :</strong>
      <div class="alert alert-info d-flex align-items-center mt-2 mb-0">
        @if($banner['image_url'])<img src="{{ $banner['image_url'] }}" height="28" class="me-2" onerror="this.style.display='none'">@endif
        <span>{{ $banner['text'] }}</span>
        @if($banner['date_from'] || $banner['date_to'])<small class="ms-auto text-muted">Affichee du {{ $banner['date_from'] }} au {{ $banner['date_to'] }}</small>@endif
      </div>
    </div>
    @endif
  </div>
</div>

{{-- Lien vers Paramètres avancés --}}
<div class="alert alert-light border d-flex align-items-center justify-content-between mb-4">
  <div>
    <i class="fas fa-tools me-2 text-secondary"></i>
    <strong>Taux de change, Opérateurs, Frais, Maintenance, Statistiques</strong>
    <span class="text-muted ms-2">— disponibles dans l'onglet dédié.</span>
  </div>
  <a href="{{ route('admin.advanced') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-right me-1"></i>Paramètres avancés
  </a>
</div>

{{-- MODAL MOT DE PASSE --}}
<div class="modal fade" id="modalPassword" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#1B365D">
        <h5 class="modal-title text-white"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('user.profile.password') }}">
          @csrf @method('PUT')
          <div class="mb-3"><label class="form-label">Mot de passe actuel</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="password" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Confirmer</label><input type="password" name="password_confirmation" class="form-control" required></div>
          <button type="submit" class="btn btn-warning w-100"><i class="fas fa-key me-2"></i>Modifier le mot de passe</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const darkCb  = document.getElementById('prefDarkMode');
const notifCb = document.getElementById('prefNotifications');
darkCb.checked  = localStorage.getItem('adminDarkMode') === '1';
notifCb.checked = localStorage.getItem('adminNotifications') !== '0';
darkCb.addEventListener('change',  () => localStorage.setItem('adminDarkMode', darkCb.checked ? '1' : '0'));
notifCb.addEventListener('change', () => localStorage.setItem('adminNotifications', notifCb.checked ? '1' : '0'));

function filterPromos(type) {
  const rows = document.querySelectorAll('.promo-row-' + type);
  let showAll = [...rows].every(r => r.style.display !== 'none');
  rows.forEach(r => { r.style.display = (showAll && !r.classList.contains('active-promo')) ? 'none' : ''; });
}
</script>
@endpush

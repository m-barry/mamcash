@extends('layouts.app')
@section('title', 'Mon profil')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-10 col-lg-11 col-12">
    <h3 class="mb-4 text-center"><i class="fas fa-user-cog me-2"></i>Mon profil</h3>

    <form method="POST" action="{{ route('user.profile.update') }}">
      @csrf @method('PUT')

      <div class="row justify-content-center">
        {{-- ── Informations personnelles ── --}}
        <div class="card col-md-4 p-3 m-2">
          <h5 class="text-center mb-3">Informations personnelles</h5>

          <div class="p-2">
            <label class="form-label">Prénom</label>
            <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}"
                   class="form-control" placeholder="Prénom" required>
          </div>
          <div class="p-2">
            <label class="form-label">Nom</label>
            <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}"
                   class="form-control" placeholder="Nom" required>
          </div>
          <div class="p-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="form-control" placeholder="Email" required>
          </div>
          <div class="p-2">
            <label class="form-label">Pays</label>
            <select name="country" class="form-select" required>
              <option value="">Choisissez un pays</option>
              @php
                $countries = ['FR'=>'France','BE'=>'Belgique','CH'=>'Suisse','CA'=>'Canada',
                              'US'=>'États-Unis','GN'=>'Guinée','SN'=>'Sénégal','CI'=>"Côte d'Ivoire",
                              'ML'=>'Mali','BF'=>'Burkina Faso','TG'=>'Togo','BJ'=>'Bénin'];
              @endphp
              @foreach($countries as $code => $name)
                <option value="{{ $code }}" {{ old('country', $user->country) === $code ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="p-2">
            <label class="form-label">Téléphone</label>
            <input type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                   class="form-control" placeholder="Numéro de téléphone" required>
          </div>
          <div class="p-2">
            <label class="form-label">Genre</label>
            <select name="gender" class="form-select">
              <option value="">Choisissez votre genre</option>
              <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Homme</option>
              <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Femme</option>
              <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Autre</option>
            </select>
          </div>
          <div class="p-2">
            <label class="form-label">Ville</label>
            <input type="text" name="city" value="{{ old('city', $user->city) }}"
                   class="form-control" placeholder="Ville">
          </div>
        </div>

        {{-- ── Adresse ── --}}
        <div class="card col-md-4 p-3 m-2">
          <h5 class="text-center mb-3">Adresse</h5>

          <div class="p-2">
            <label class="form-label">Rue</label>
            <input type="text" name="street" value="{{ old('street', $address->street ?? '') }}"
                   class="form-control" placeholder="Rue">
          </div>
          <div class="p-2">
            <label class="form-label">Numéro de rue</label>
            <input type="text" name="house_number" value="{{ old('house_number', $address->house_number ?? '') }}"
                   class="form-control" placeholder="N°">
          </div>
          <div class="p-2">
            <label class="form-label">Code postal</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $address->zip_code ?? $user->postal) }}"
                   class="form-control" placeholder="Code postal">
          </div>
          <div class="p-2">
            <label class="form-label">Ville (adresse)</label>
            <input type="text" name="address_city" value="{{ old('address_city', $address->city ?? $user->city) }}"
                   class="form-control" placeholder="Ville">
          </div>
        </div>
      </div>

      {{-- Boutons --}}
      <div class="d-flex justify-content-center gap-3 flex-wrap mb-4 mt-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-danger px-4 py-2 rounded-pill shadow-sm">
          <i class="fas fa-times me-2"></i>Annuler
        </a>
        <button type="submit" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
          <i class="fas fa-check me-2"></i><strong>Enregistrer</strong>
        </button>
      </div>
    </form>

    {{-- ── Changer mot de passe ── --}}
    <div class="row justify-content-center mb-5">
      <div class="col-md-5 text-center">
        <button class="btn btn-danger px-4 py-2 rounded-pill shadow-sm mb-3"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#passwordCollapse"
                aria-expanded="false"
                aria-controls="passwordCollapse">
          <i class="fas fa-lock me-2"></i>Changer le mot de passe
        </button>
        <div class="collapse" id="passwordCollapse">
          <div class="card p-4 shadow-sm text-start">
            <form method="POST" action="{{ route('user.profile.password') }}">
              @csrf @method('PUT')
              <div class="mb-3">
                <label class="form-label">Mot de passe actuel</label>
                <input type="password" name="current_password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirmer</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-warning w-100">
                <i class="fas fa-key me-2"></i>Modifier le mot de passe
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  {{-- Rouvrir le collapse si erreur de validation mot de passe --}}
  @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
    document.addEventListener('DOMContentLoaded', function () {
      var el = document.getElementById('passwordCollapse');
      new bootstrap.Collapse(el, { show: true });
    });
  @endif
</script>
@endpush
@endsection

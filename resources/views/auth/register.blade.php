@extends('layouts.app')
@section('title', 'Inscription')
@section('content')
<div class="d-flex justify-content-center align-items-center py-4">
<div class="card shadow p-4" style="max-width:650px;width:100%;border-radius:10px;background:#f8f9fa;">
<div class="text-center mb-4">
  <a href="{{ route('home') }}">
    <img src="{{ asset('assets/images/MAMCash-logo-light.svg') }}" alt="MAMCash" height="70">
  </a>
</div>
<h3 class="text-center mb-4">Créer un compte</h3>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('register.post') }}">@csrf
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Prénom</label>
<input type="text" name="firstname" value="{{ old('firstname') }}" class="form-control form-control-lg" placeholder="Prénom" required></div>
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Nom</label>
<input type="text" name="lastname" value="{{ old('lastname') }}" class="form-control form-control-lg" placeholder="Nom" required></div>
</div>
<div class="mb-3"><label class="form-label fw-semibold">Email</label>
<input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="Email" required></div>
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Mot de passe</label>
<input type="password" name="password" class="form-control form-control-lg" placeholder="Mot de passe" required></div>
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Confirmer</label>
<input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirmer le mot de passe" required></div>
</div>
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Date de naissance</label>
<input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control form-control-lg"></div>
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Genre</label>
<select name="gender" class="form-select form-select-lg"><option value="">Sélectionner</option>
<option value="male" {{ old('gender')=='male'?'selected':' ' }}>Homme</option>
<option value="female" {{ old('gender')=='female'?'selected':' ' }}>Femme</option></select></div>
</div>
<div class="mb-3"><label class="form-label fw-semibold">Pays</label>
<select name="country" class="form-select form-select-lg" required>
<option value="">Sélectionnez votre pays</option>
<option value="FR">France</option><option value="BE">Belgique</option>
<option value="CH">Suisse</option><option value="CA">Canada</option>
<option value="US">États-Unis</option><option value="GN">Guinée</option>
<option value="SN">Sénégal</option><option value="CI">Côte d'Ivoire</option>
<option value="ML">Mali</option><option value="BF">Burkina Faso</option>
<option value="TG">Togo</option><option value="BJ">Bénin</option>
</select></div>
<div class="mb-3"><label class="form-label fw-semibold">Téléphone</label>
<input type="tel" name="phone_number" value="{{ old('phone_number') }}" class="form-control form-control-lg" placeholder="Numéro de téléphone" required></div>
<div class="mb-3"><label class="form-label fw-semibold">Adresse</label>
<input type="text" name="addresse" value="{{ old('addresse') }}" class="form-control form-control-lg" placeholder="Adresse"></div>
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Code postal</label>
<input type="text" name="postal" value="{{ old('postal') }}" class="form-control form-control-lg" placeholder="Code postal"></div>
<div class="col-md-6 mb-3"><label class="form-label fw-semibold">Ville</label>
<input type="text" name="city" value="{{ old('city') }}" class="form-control form-control-lg" placeholder="Ville"></div>
</div>
<button type="submit" class="btn btn-primary w-100 btn-lg mb-2">Créer mon compte</button>
<div class="text-center"><a href="{{ route('login') }}" class="text-primary small">Déjà un compte ? Se connecter</a></div>
</form></div></div>
@endsection

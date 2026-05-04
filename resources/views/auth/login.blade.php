@extends('layouts.app')
@section('title', 'Connexion')
@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height:80vh">
<div class="card shadow p-4" style="max-width:420px;width:100%;border-radius:10px;background:#f8f9fa;">
<div class="text-center mb-4">
  <a href="{{ route('home') }}">
    <img src="{{ asset('assets/images/mamcash-logo-light.svg') }}" alt="MAMCash" height="70">
  </a>
</div>
<h3 class="text-center mb-4">Connexion</h3>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('login.post') }}">@csrf
<div class="mb-3"><label class="form-label fw-semibold">Email</label>
<input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="Entrez votre email" required></div>
<div class="mb-3"><label class="form-label fw-semibold">Mot de passe</label>
<input type="password" name="password" class="form-control form-control-lg" placeholder="Mot de passe" required></div>
<div class="mb-3 form-check"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label" for="remember">Se souvenir de moi</label></div>
<button type="submit" class="btn btn-primary w-100 btn-lg mb-2">Connexion</button>
<div class="text-center mb-2"><a href="{{ route('password.request') }}" class="text-primary small">Mot de passe oublié ?</a></div>
<a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Créer un compte</a>
</form></div></div>
@endsection

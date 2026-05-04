@extends('layouts.app')
@section('title', 'Mot de passe oublie')
@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height:80vh">
<div class="card shadow p-4" style="max-width:420px;width:100%;border-radius:10px;background:#f8f9fa;">
<div class="text-center mb-3"><h3 class="text-primary fw-bold">MAMCash</h3></div>
<h4 class="text-center mb-3">Mot de passe oublie ?</h4>
<p class="text-muted small mb-4">Entrez votre email, nous vous enverrons un lien de reinitialisation.</p>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('password.email') }}">@csrf
<div class="mb-3"><label class="form-label fw-semibold">Email</label>
<input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="Votre email" required></div>
<button type="submit" class="btn btn-primary w-100 btn-lg mb-2">Envoyer le lien</button>
<div class="text-center"><a href="{{ route('login') }}" class="text-primary small">Retour connexion</a></div>
</form></div></div>
@endsection

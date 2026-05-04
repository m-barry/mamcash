<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MAMCash Admin — @yield('title', 'Tableau de bord')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .navbar-admin {
            background: linear-gradient(135deg, #1B365D 0%, #2c4a6b 100%) !important;
            box-shadow: 0 2px 8px rgba(27, 54, 93, 0.3);
        }
        .navbar-admin .nav-link { font-weight: bold; color: #fff !important; }
        .navbar-admin .nav-link:hover { color: #FFD700 !important; }
        .navbar-admin .navbar-brand { color: #fff !important; font-weight: bold; }
        .navbar-admin .navbar-brand:hover { color: #FFD700 !important; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,215,0,0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .form-control, .form-select { background-color: #e8f0fe !important; border: 1px solid #d1e7dd !important; }
        .page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body style="background-color:#eef2f7;">
<div class="page-wrapper">

<nav class="navbar navbar-expand-lg navbar-admin">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/MAMCash-logo-navbar.svg') }}" alt="MAMCash" height="40"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
            <span style="display:none">MAMCash</span>
            &nbsp;Tableau de bord
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navAdmin">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'text-warning' : '' }}"
                       href="{{ route('admin.users') }}">
                        <i class="fas fa-users"></i> Gestion des utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.transactions') ? 'text-warning' : '' }}"
                       href="{{ route('admin.transactions') }}">
                        <i class="fas fa-exchange-alt"></i> Transactions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'text-warning' : '' }}"
                       href="{{ route('admin.users') }}">
                        <i class="fas fa-address-book"></i> Bénéficiaires
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'text-warning' : '' }}"
                       href="{{ route('admin.settings') }}">
                        <i class="fas fa-cogs"></i> Paramètres
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.advanced') ? 'text-warning' : '' }}"
                       href="{{ route('admin.advanced') }}">
                        <i class="fas fa-tools"></i> Paramètres avancés
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn nav-link" type="submit">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-2">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<main class="container mt-3">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</div>{{-- /page-wrapper --}}
</body>
</html>

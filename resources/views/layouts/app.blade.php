<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MAMCash') }} — @yield('title', 'Transfert d\'argent')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        /* ── Navbar MAMCash ── */
        .navbar-MAMCash {
            background: linear-gradient(135deg, #1B365D 0%, #2c4a6b 100%) !important;
            box-shadow: 0 2px 8px rgba(27, 54, 93, 0.3);
        }
        .navbar-MAMCash .nav-link {
            font-size: 1rem;
            font-weight: bold;
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
        .navbar-MAMCash .nav-link:hover { color: #FFD700 !important; }
        .navbar-MAMCash .navbar-brand { font-size: 1.5rem; font-weight: bold; color: #ffffff !important; transition: transform 0.3s ease; }
        .navbar-MAMCash .navbar-brand:hover { color: #FFD700 !important; transform: scale(1.05); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,215,0,0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .nav-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,215,0,0.12);
            border: 1.5px solid rgba(255,215,0,0.35);
            border-radius: 50px;
            padding: 4px 12px 4px 5px;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .nav-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #FFD700;
            color: #1B365D;
            font-size: 0.8rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }
        .nav-user-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(34,197,94,0.3);
        }
        /* ── Formulaires ── */
        .form-control, .form-select {
            background-color: #e8f0fe !important;
            border: 1px solid #d1e7dd !important;
        }
        /* ── Step indicator ── */
        .step-circle {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #dee2e6; color: #495057; font-weight: bold;
            border: 2px solid #adb5bd;
        }
        .step-circle.active {
            background: #007bff; color: #fff; border-color: #007bff;
        }
        @keyframes scroll-text {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        /* ── Page wrapper ── */
        .page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            min-height: 100vh;
        }

        /* ── Harmonisation mobile globale ── */
        @media (max-width: 768px) {
            body {
                font-size: 15px;
                line-height: 1.35;
            }

            h1, .h1 { font-size: 1.45rem; }
            h2, .h2 { font-size: 1.3rem; }
            h3, .h3 { font-size: 1.15rem; }
            h4, .h4 { font-size: 1.05rem; }
            h5, .h5 { font-size: 1rem; }
            h6, .h6 { font-size: 0.95rem; }

            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .card {
                border-radius: 10px;
            }

            .form-label {
                font-size: 0.88rem;
                margin-bottom: 0.3rem;
            }

            .form-control,
            .form-select,
            .input-group-text {
                min-height: 42px;
                font-size: 0.92rem;
            }

            .form-control-lg,
            .form-select-lg {
                min-height: 44px;
                padding: 0.45rem 0.75rem;
                font-size: 0.95rem;
            }

            .btn {
                min-height: 42px;
                font-size: 0.9rem;
                border-radius: 0.55rem;
                padding: 0.45rem 0.8rem;
            }

            .btn-lg {
                min-height: 44px;
                font-size: 0.95rem;
                padding: 0.5rem 0.95rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body style="background-color:#eef2f7;">
<div class="page-wrapper">

    {{-- ── Navbar utilisateur ── --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-MAMCash">
        <div class="container">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">
                <img src="{{ asset('assets/images/mamcash-logo-navbar.svg') }}" alt="MAMCash" height="40"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span style="display:none">MAMCash</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                    @php
                        $userCountry = strtolower(auth()->user()->country ?? '');
                    @endphp
                    <li class="nav-item me-2">
                        <span class="nav-user-badge">
                            <span class="nav-user-avatar">{{ mb_substr(auth()->user()->firstname ?? auth()->user()->name ?? '?', 0, 1) }}</span>
                            <span>{{ auth()->user()->firstname ?? auth()->user()->name }}</span>
                            @if($userCountry)
                                <img src="https://flagcdn.com/w20/{{ $userCountry }}.png"
                                     width="20" height="14"
                                     title="{{ strtoupper($userCountry) }}"
                                     style="border-radius:2px;box-shadow:0 0 0 1px rgba(255,255,255,0.25);vertical-align:middle"
                                     onerror="this.style.display='none'">
                            @endif
                            <span class="nav-user-dot"></span>
                        </span>
                    </li>
                    @endauth
                    @if(!auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.profile') ? 'text-warning' : '' }}"
                               href="{{ route('user.profile') }}">
                                <i class="fas fa-user-circle"></i> Mon profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.history') ? 'text-warning' : '' }}"
                               href="{{ route('user.history') }}">
                                <i class="fas fa-history"></i> Historique des virements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.contacts') ? 'text-warning' : '' }}"
                               href="{{ route('user.contacts') }}">
                                <i class="fas fa-user-friends"></i> Bénéficiaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.reclamation') ? 'text-warning' : '' }}"
                               href="{{ route('user.reclamation') }}">
                                <i class="fas fa-envelope"></i> Nous contacter
                            </a>
                        </li>
                    @endif
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
    @else
    <nav class="navbar navbar-expand-lg navbar-MAMCash">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/mamcash-logo-navbar.svg') }}" alt="MAMCash" height="40"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span style="display:none">MAMCash</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navGuest">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navGuest">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Inscription
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-money-bill-wave"></i> Envoyer de l'argent
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-envelope"></i> Nous contacter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    {{-- ── Alertes flash + Contenu ── --}}
    <main class="container pt-2 pb-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</div>{{-- /.page-wrapper --}}
</body>
</html>


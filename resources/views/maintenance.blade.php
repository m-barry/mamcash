<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAMCash — Maintenance en cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1B365D 0%, #2c4a6b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .maint-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        .maint-logo {
            font-size: 2rem;
            font-weight: 900;
            color: #1B365D;
            letter-spacing: 1px;
            margin-bottom: .25rem;
        }
        .maint-logo span { color: #FFD700; }
        .maint-logo-sub {
            font-size: .8rem;
            color: #a0aec0;
            margin-bottom: 2rem;
        }
        .maint-icon {
            font-size: 4.5rem;
            color: #FFD700;
            margin-bottom: 1.25rem;
            animation: gear-spin 4s linear infinite;
        }
        @keyframes gear-spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .maint-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1B365D;
            margin-bottom: .5rem;
        }
        .maint-subtitle {
            font-size: 1rem;
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .maint-info {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-size: .875rem;
            color: #4a5568;
            margin-bottom: 1.5rem;
        }
        .maint-info i { color: #1B365D; margin-right: .4rem; }
        .maint-footer {
            font-size: .75rem;
            color: #a0aec0;
            margin-top: 1.5rem;
        }
        .maint-footer a { color: #1B365D; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="maint-card">
        <div class="maint-logo">MAM<span>Send</span></div>
        <div class="maint-logo-sub">Transfert d'argent vers l'Afrique de l'Ouest</div>

        <div class="maint-icon">
            <i class="fas fa-cog"></i>
        </div>

        <div class="maint-title">Service en maintenance</div>
        <div class="maint-subtitle">
            Nous améliorons notre plateforme pour vous offrir une meilleure expérience.<br>
            Le service sera de nouveau disponible très prochainement.
        </div>

        <div class="maint-info">
            <i class="fas fa-clock"></i>
            <strong>Retour prévu :</strong> dans quelques instants. Merci de votre patience.
        </div>

        <div class="maint-info">
            <i class="fas fa-envelope"></i>
            Pour toute urgence, contactez-nous à <strong>support@MAMCash.com</strong>
        </div>

        <div class="maint-footer">
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.advanced') }}#tabMaintenance" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-tools me-1"></i>Réactiver le service
                </a>
            @else
                Vous êtes administrateur ?
                <a href="{{ route('login') }}" style="color:#1B365D;font-weight:600;">Se connecter</a>
            @endif
        </div>
    </div>
</body>
</html>

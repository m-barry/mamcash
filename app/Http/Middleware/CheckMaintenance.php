<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        // Laisser passer les webhooks externes
        if ($request->is('stripe/webhook') || $request->is('orange-money/*')) {
            return $next($request);
        }

        // Laisser passer la page de maintenance elle-même
        if ($request->is('maintenance')) {
            return $next($request);
        }

        // Vérifier si le mode maintenance est activé
        if (!(bool) Setting::get('maintenance_mode', '0')) {
            return $next($request);
        }

        // Maintenance active : laisser passer les admins connectés (ils accèdent partout)
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Laisser passer les routes d'auth pour que l'admin puisse se connecter
        if ($request->is('login', 'register') || $request->routeIs('login', 'login.post', 'logout', 'register', 'register.post', 'password.request')) {
            return $next($request);
        }

        // Laisser passer toutes les routes admin (sécurité supplémentaire)
        if ($request->is('admin/*') || $request->is('admin')) {
            return $next($request);
        }

        // Tout le reste est bloqué
        return redirect()->route('maintenance');
    }
}

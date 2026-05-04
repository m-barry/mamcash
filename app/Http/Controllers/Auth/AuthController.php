<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            // Utilisateur normal : si maintenance active, le déconnecter pour permettre la connexion admin
            if ((bool) \App\Models\Setting::get('maintenance_mode', '0')) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return view('auth.login')->with('info', 'Veuillez vous connecter avec un compte administrateur.');
            }
            return redirect()->route('user.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Vérifier que le compte est actif
            if (! Auth::user()->active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstname'   => ['required', 'string', 'max:100'],
            'lastname'    => ['required', 'string', 'max:100'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'phone_number'=> ['required', 'string', 'max:30', 'unique:users'],
            'country'     => ['required', 'string', 'max:10'],
            'city'        => ['nullable', 'string', 'max:100'],
            'gender'      => ['nullable', 'in:male,female,other'],
            'birth_date'  => ['nullable', 'date', 'before:today'],
            'addresse'    => ['nullable', 'string', 'max:255'],
            'postal'      => ['nullable', 'string', 'max:20'],
        ]);

        // Créer un compte IBAN
        $account = Account::create([
            'iban'         => 'MS' . strtoupper(Str::random(18)),
            'created_date' => now()->toDateString(),
        ]);

        // Rôle utilisateur par défaut
        $role = Role::where('name', 'ROLE_USER')->first();

        $user = User::create([
            'firstname'    => $validated['firstname'],
            'lastname'     => $validated['lastname'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'country'      => $validated['country'],
            'city'         => $validated['city'] ?? null,
            'gender'       => $validated['gender'] ?? null,
            'birth_date'   => $validated['birth_date'] ?? null,
            'addresse'     => $validated['addresse'] ?? null,
            'postal'       => $validated['postal'] ?? null,
            'active'       => true,
            'created_date' => now()->toDateString(),
            'account_id'   => $account->id,
            'role_id'      => $role?->id,
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard')
            ->with('success', 'Compte créé avec succès. Bienvenue sur MAMCash !');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Si maintenance active, rediriger vers login plutôt que home (qui serait bloqué)
        if ((bool) \App\Models\Setting::get('maintenance_mode', '0')) {
            return redirect()->route('login');
        }

        return redirect()->route('home');
    }
}

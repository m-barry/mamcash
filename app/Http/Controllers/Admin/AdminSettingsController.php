<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\Operator;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $admin              = auth()->user();
        $promotionsTransfer = Promotion::where('type', 'TRANSFER')->orderByDesc('id')->get();
        $promotionsRecharge = Promotion::where('type', 'RECHARGE')->orderByDesc('id')->get();
        $operators          = Operator::orderBy('name')->get();

        // Bannière publicitaire
        $banner = [
            'text'      => Setting::get('banner_text', ''),
            'image_url' => Setting::get('banner_image_url', ''),
            'link'      => Setting::get('banner_link', ''),
            'date_from' => Setting::get('banner_date_from', ''),
            'date_to'   => Setting::get('banner_date_to', ''),
            'active'    => (bool) Setting::get('banner_active', '0'),
        ];

        // Taux de change
        $rates = [
            'EUR' => Setting::get('rate_EUR_GNF', '10000'),
            'USD' => Setting::get('rate_USD_GNF', '9300'),
            'CAD' => Setting::get('rate_CAD_GNF', '6800'),
        ];

        // Frais de transfert
        $feeTiersJson = Setting::get('fee_tiers', '{}');
        $feeTiers     = json_decode($feeTiersJson, true) ?? [];

        // Mode maintenance
        $maintenanceMode = (bool) Setting::get('maintenance_mode', '0');

        // Statistiques
        $adminRoleId = \App\Models\Role::where('name', 'ROLE_ADMIN')->value('id');
        $stats = [
            'total_transactions'  => Transaction::count(),
            'total_volume'        => Transaction::sum('amount'),
            'transactions_month'  => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->count(),
            'volume_month'        => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->sum('amount'),
            'total_fees'          => Transaction::sum('fee'),
            'fees_month'          => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->sum('fee'),
            'total_users'         => User::when($adminRoleId, fn($q) => $q->where('role_id', '!=', $adminRoleId))->count(),
            'active_users'        => User::when($adminRoleId, fn($q) => $q->where('role_id', '!=', $adminRoleId))->where('active', true)->count(),
            'transfers_count'     => Transaction::where('type', 'TRANSFERT')->count(),
            'recharge_count'      => Transaction::where('type', 'RECHARGE')->count(),
        ];

        return view('admin.settings', compact(
            'admin', 'promotionsTransfer', 'promotionsRecharge', 'banner'
        ));
    }

    // ── Paramètres avancés ───────────────────────────────────────────────────
    public function advancedSettings()
    {
        $operators = Operator::orderBy('name')->get();

        $rates = [
            'EUR' => Setting::get('rate_EUR_GNF', '10000'),
            'USD' => Setting::get('rate_USD_GNF', '9300'),
            'CAD' => Setting::get('rate_CAD_GNF', '6800'),
        ];

        $feeTiersJson = Setting::get('fee_tiers', '{}');
        $feeTiers     = json_decode($feeTiersJson, true) ?? [];

        $maintenanceMode = (bool) Setting::get('maintenance_mode', '0');

        $adminRoleId = \App\Models\Role::where('name', 'ROLE_ADMIN')->value('id');
        $stats = [
            'total_transactions'  => Transaction::count(),
            'total_volume'        => Transaction::sum('amount'),
            'transactions_month'  => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->count(),
            'volume_month'        => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->sum('amount'),
            'total_fees'          => Transaction::sum('fee'),
            'fees_month'          => Transaction::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)->sum('fee'),
            'total_users'         => User::when($adminRoleId, fn($q) => $q->where('role_id', '!=', $adminRoleId))->count(),
            'active_users'        => User::when($adminRoleId, fn($q) => $q->where('role_id', '!=', $adminRoleId))->where('active', true)->count(),
            'transfers_count'     => Transaction::where('type', 'TRANSFERT')->count(),
            'recharge_count'      => Transaction::where('type', 'RECHARGE')->count(),
        ];

        return view('admin.advanced-settings', compact('operators', 'rates', 'feeTiers', 'maintenanceMode', 'stats'));
    }

    // ── Profil admin ─────────────────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname'  => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($admin->id)],
        ]);

        $admin->update($validated);

        return back()->with('success_profile', 'Profil mis à jour avec succès.');
    }

    // ── Taux de change ────────────────────────────────────────────────────────
    public function updateRates(Request $request)
    {
        $validated = $request->validate([
            'rate_EUR' => ['required', 'numeric', 'min:1'],
            'rate_USD' => ['required', 'numeric', 'min:1'],
            'rate_CAD' => ['required', 'numeric', 'min:1'],
        ]);

        Setting::set('rate_EUR_GNF', $validated['rate_EUR']);
        Setting::set('rate_USD_GNF', $validated['rate_USD']);
        Setting::set('rate_CAD_GNF', $validated['rate_CAD']);

        return back()->with('success_rates', 'Taux de change mis à jour.');
    }

    // ── Opérateurs Mobile Money ───────────────────────────────────────────────
    public function addOperator(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'country'     => ['required', 'string', 'max:10'],
            'code_prefix' => ['nullable', 'string', 'max:30'],
            'logo_url'    => ['nullable', 'url', 'max:255'],
        ]);

        $validated['active'] = true;
        Operator::create($validated);

        return back()->with('success_operators', 'Opérateur ajouté.');
    }

    public function toggleOperator(int $id)
    {
        $op = Operator::findOrFail($id);
        $op->update(['active' => !$op->active]);
        return back()->with('success_operators', 'Statut opérateur mis à jour.');
    }

    public function toggleComingSoon(int $id)
    {
        $op = Operator::findOrFail($id);
        $op->update(['show_coming_soon' => !$op->show_coming_soon]);
        $msg = $op->show_coming_soon ? 'Badge « Bientôt disponible » activé.' : 'Opérateur masqué de la liste.';
        return back()->with('success_operators', $msg);
    }

    public function deleteOperator(int $id)
    {
        Operator::findOrFail($id)->delete();
        return back()->with('success_operators', 'Opérateur supprimé.');
    }

    // ── Frais de transfert ────────────────────────────────────────────────────
    public function updateFees(Request $request)
    {
        $validated = $request->validate([
            'tiers'            => ['required', 'array'],
            'tiers.*.limit'    => ['required', 'integer', 'min:1'],
            'tiers.*.fee'      => ['required', 'numeric', 'min:0'],
            'above_base'       => ['required', 'numeric', 'min:0'],
            'above_step'       => ['required', 'integer', 'min:1'],
            'above_increment'  => ['required', 'numeric', 'min:0'],
        ]);

        $tiersMap = [];
        foreach ($validated['tiers'] as $tier) {
            $tiersMap[(string) $tier['limit']] = (float) $tier['fee'];
        }
        $tiersMap['above_base']      = (float) $validated['above_base'];
        $tiersMap['above_step']      = (int)   $validated['above_step'];
        $tiersMap['above_increment'] = (float) $validated['above_increment'];

        Setting::set('fee_tiers', json_encode($tiersMap));

        return back()->with('success_fees', 'Frais de transfert mis à jour.');
    }

    // ── Mode maintenance ──────────────────────────────────────────────────────
    public function toggleMaintenance(Request $request)
    {
        $current = (bool) Setting::get('maintenance_mode', '0');
        Setting::set('maintenance_mode', $current ? '0' : '1');
        $msg = $current ? 'Mode maintenance désactivé.' : 'Mode maintenance activé.';
        return back()->with('success_maintenance', $msg);
    }

    // ── Promotions ────────────────────────────────────────────────────────────
    public function addPromotion(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:promotion'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount'    => ['required', 'integer', 'min:1', 'max:100'],
            'type'        => ['required', 'in:TRANSFER,RECHARGE'],
        ]);

        $validated['active'] = $request->boolean('active', true);
        Promotion::create($validated);

        return back()->with('success_promo_' . strtolower($validated['type']), 'Promotion ajoutée.');
    }

    public function deleteAllPromotions(string $type)
    {
        Promotion::where('type', strtoupper($type))->delete();
        return back()->with('success_promo_' . strtolower($type), 'Toutes les promotions supprimées.');
    }

    public function saveBanner(Request $request)
    {
        $request->validate([
            'banner_text'      => ['nullable', 'string', 'max:500'],
            'banner_image_url' => ['nullable', 'max:500'],
            'banner_link'      => ['nullable', 'max:500'],
            'banner_date_from' => ['nullable', 'date'],
            'banner_date_to'   => ['nullable', 'date'],
        ]);

        Setting::set('banner_text',      $request->input('banner_text', ''));
        Setting::set('banner_image_url', $request->input('banner_image_url', ''));
        Setting::set('banner_link',      $request->input('banner_link', ''));
        Setting::set('banner_date_from', $request->input('banner_date_from', ''));
        Setting::set('banner_date_to',   $request->input('banner_date_to', ''));
        Setting::set('banner_active',    $request->boolean('banner_active') ? '1' : '0');

        return back()->with('success_banner', 'Bannière mise à jour.');
    }

    public function togglePromotion(int $id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->update(['active' => !$promo->active]);
        return back()->with('success', 'Statut promotion mis à jour.');
    }

    public function deletePromotion(int $id)
    {
        Promotion::findOrFail($id)->delete();
        return back()->with('success', 'Promotion supprimée.');
    }
}


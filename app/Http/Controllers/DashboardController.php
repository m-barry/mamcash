<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    // Page d'accueil publique (convertisseur de devises)
    public function index()
    {
        $promotionTransfer  = Promotion::where('active', true)->where('type', 'TRANSFER')->first();
        $promotionRecharge  = Promotion::where('active', true)->where('type', 'RECHARGE')->first();
        $promotion          = $promotionTransfer; // compat bannière promo (premier actif)
        $banner             = $this->getBanner();
        $rates              = $this->getExchangeRates();
        $feeTiers           = $this->getFeeConfig();
        $operators          = Operator::orderBy('active', 'desc')->orderBy('name')->get();
        return view('home', compact('promotion', 'promotionTransfer', 'promotionRecharge', 'banner', 'rates', 'feeTiers', 'operators'));
    }

    // Dashboard utilisateur connecté
    public function userDashboard()
    {
        $user               = auth()->user();
        $promotionTransfer  = Promotion::where('active', true)->where('type', 'TRANSFER')->first();
        $promotionRecharge  = Promotion::where('active', true)->where('type', 'RECHARGE')->first();
        $promotion          = $promotionTransfer; // compat bannière existante
        $operators          = Operator::orderBy('active', 'desc')->orderBy('name')->get();
        $contacts           = $user->contacts;
        $banner             = $this->getBanner();

        // Taux de change depuis une API publique (sans clé)
        $rates    = $this->getExchangeRates();
        $feeTiers = $this->getFeeConfig();

        return view('user.dashboard', compact('user', 'promotion', 'promotionTransfer', 'promotionRecharge', 'rates', 'operators', 'contacts', 'banner', 'feeTiers'));
    }

    private function getBanner(): array
    {
        $today  = now()->toDateString();
        $active = (bool) Setting::get('banner_active', '0');
        $from   = Setting::get('banner_date_from', '');
        $to     = Setting::get('banner_date_to', '');
        // Vérifier les dates si renseignées
        if ($active && $from && $today < $from) $active = false;
        if ($active && $to   && $today > $to)   $active = false;
        return [
            'text'      => Setting::get('banner_text', ''),
            'image_url' => Setting::get('banner_image_url', ''),
            'link'      => Setting::get('banner_link', ''),
            'date_from' => $from,
            'date_to'   => $to,
            'active'    => $active,
        ];
    }

    private function getExchangeRates(): array
    {
        // Priorité 1 : taux configurés par l'admin en base de données
        $eurDb = Setting::get('rate_EUR_GNF', null);
        $usdDb = Setting::get('rate_USD_GNF', null);
        $cadDb = Setting::get('rate_CAD_GNF', null);

        if ($eurDb && $usdDb && $cadDb) {
            return [
                'EUR' => (float) $eurDb,
                'USD' => (float) $usdDb,
                'CAD' => (float) $cadDb,
            ];
        }

        // Priorité 2 : API externe (si taux admin non configurés)
        try {
            $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/GNF');
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'EUR' => $data['rates']['EUR'] > 0 ? round(1 / $data['rates']['EUR'], 2) : 10000,
                    'USD' => $data['rates']['USD'] > 0 ? round(1 / $data['rates']['USD'], 2) : 9300,
                    'CAD' => $data['rates']['CAD'] > 0 ? round(1 / $data['rates']['CAD'], 2) : 6800,
                ];
            }
        } catch (\Exception $e) {
            // API indisponible, taux par défaut ci-dessous
        }

        return ['EUR' => 10000, 'USD' => 9300, 'CAD' => 6800];
    }

    private function getFeeConfig(): array
    {
        $default = [
            '50' => 2, '100' => 4, '150' => 6, '200' => 8, '250' => 10,
            '300' => 12, '350' => 14, '400' => 16, '450' => 18, '500' => 20,
            'above_base' => 20, 'above_step' => 50, 'above_increment' => 2,
        ];

        $json = Setting::get('fee_tiers', '');
        if (! $json) {
            return $default;
        }

        $tiers = json_decode($json, true);
        return is_array($tiers) && count($tiers) ? $tiers : $default;
    }
}

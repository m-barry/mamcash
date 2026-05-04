<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRates extends Command
{
    protected $signature   = 'rates:update';
    protected $description = 'Récupère les taux de change depuis l\'API et les enregistre en base';

    public function handle(): int
    {
        $this->info('Mise à jour des taux de change...');

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => app()->isProduction()])
                ->get('https://open.er-api.com/v6/latest/GNF');

            if (! $response->successful()) {
                $this->error('API indisponible (HTTP ' . $response->status() . ')');
                Log::error('rates:update — API retourne HTTP ' . $response->status());
                return self::FAILURE;
            }

            $data = $response->json();

            if (empty($data['rates'])) {
                $this->error('Réponse API invalide (pas de clé "rates")');
                Log::error('rates:update — réponse API invalide', ['body' => $data]);
                return self::FAILURE;
            }

            $rates = $data['rates'];

            $eur = ($rates['EUR'] ?? 0) > 0 ? round(1 / $rates['EUR'], 2) : null;
            $usd = ($rates['USD'] ?? 0) > 0 ? round(1 / $rates['USD'], 2) : null;
            $cad = ($rates['CAD'] ?? 0) > 0 ? round(1 / $rates['CAD'], 2) : null;

            if (! $eur || ! $usd || ! $cad) {
                $this->error('Taux manquants dans la réponse API');
                Log::error('rates:update — taux manquants', compact('eur', 'usd', 'cad'));
                return self::FAILURE;
            }

            Setting::set('rate_EUR_GNF', $eur);
            Setting::set('rate_USD_GNF', $usd);
            Setting::set('rate_CAD_GNF', $cad);

            $this->info("EUR → {$eur} GNF");
            $this->info("USD → {$usd} GNF");
            $this->info("CAD → {$cad} GNF");
            $this->info('Taux mis à jour avec succès.');

            Log::info('rates:update — taux mis à jour', compact('eur', 'usd', 'cad'));

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
            Log::error('rates:update — exception', ['message' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valeurs par défaut
        DB::table('settings')->insert([
            ['key' => 'rate_EUR_GNF',     'value' => '10000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'rate_USD_GNF',     'value' => '9300',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'rate_CAD_GNF',     'value' => '6800',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'fee_tiers',        'value' => '{"50":2,"100":4,"150":6,"200":8,"250":10,"300":12,"350":14,"400":16,"450":18,"500":20,"above_base":20,"above_step":50,"above_increment":2}', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

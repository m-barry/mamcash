<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('country', 10)->default('GN');
            $table->string('code_prefix', 30)->nullable();
            $table->boolean('active')->default(true);
            $table->string('logo_url', 255)->nullable();
            $table->timestamps();
        });

        // Opérateurs par défaut
        DB::table('operators')->insert([
            ['name' => 'Orange Money',   'country' => 'GN', 'code_prefix' => '62', 'active' => true, 'logo_url' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MTN Mobile',     'country' => 'GN', 'code_prefix' => '67', 'active' => true, 'logo_url' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cellcom Money',  'country' => 'GN', 'code_prefix' => '65', 'active' => true, 'logo_url' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};

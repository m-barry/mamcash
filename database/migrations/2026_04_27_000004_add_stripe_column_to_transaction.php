<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id', 100)->nullable()->after('fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (Schema::hasColumn('transaction', 'stripe_payment_intent_id')) {
                $table->dropColumn('stripe_payment_intent_id');
            }
        });
    }
};

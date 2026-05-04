<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'om_order_id')) {
                $table->string('om_order_id', 50)->nullable()->after('operator');
            }
            if (! Schema::hasColumn('transaction', 'om_pay_token')) {
                $table->string('om_pay_token', 100)->nullable()->after('om_order_id');
            }
            if (! Schema::hasColumn('transaction', 'om_notif_token')) {
                $table->string('om_notif_token', 100)->nullable()->after('om_pay_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn(['om_order_id', 'om_pay_token', 'om_notif_token']);
        });
    }
};

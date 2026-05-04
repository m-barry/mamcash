<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('last_modified_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('destination_iban', 100)->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->string('receiver', 100)->nullable();
            $table->string('receiver_number_phone', 30)->nullable();
            $table->string('sender', 100)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('type', 30)->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('stripe_payment_intent_id', 100)->nullable();
            $table->string('operator', 50)->nullable();
            $table->string('om_order_id', 50)->nullable();
            $table->string('om_pay_token', 100)->nullable();
            $table->string('om_notif_token', 100)->nullable();
            $table->decimal('amount_sent', 15, 2)->nullable();
            $table->string('currency', 5)->default('EUR');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};

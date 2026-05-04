<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('last_modified_date')->nullable();
            $table->string('name', 50)->nullable();
        });

        Schema::create('account', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('last_modified_date')->nullable();
            $table->string('iban', 50)->nullable();
        });

        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('house_number', 20)->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('last_modified_date')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('county', 100)->nullable();
            $table->string('street', 150)->nullable();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('country', 10)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('firstname', 100)->nullable();
            $table->string('iban', 50)->nullable();
            $table->string('lastname', 100)->nullable();
            $table->string('relationship', 50)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });

        Schema::create('promotion', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false);
            $table->enum('type', ['TRANSFER', 'RECHARGE'])->default('TRANSFER');
            $table->string('code', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
        });

        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('code', 10)->nullable();
        });

        Schema::create('cinetpay_transaction', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('recharge_promotion', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharge_promotion');
        Schema::dropIfExists('cinetpay_transaction');
        Schema::dropIfExists('country');
        Schema::dropIfExists('promotion');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('address');
        Schema::dropIfExists('account');
        Schema::dropIfExists('role');
    }
};

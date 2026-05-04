<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // The default Laravel users table has a non-nullable 'name' column
            // that is unused in this app (we use firstname/lastname instead).
            // Make it nullable so registration doesn't fail.
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable(false)->change();
            }
        });
    }
};

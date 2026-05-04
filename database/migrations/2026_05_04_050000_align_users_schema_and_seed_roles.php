<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname', 100)->nullable()->after('id');
            }
            if (! Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname', 100)->nullable()->after('firstname');
            }
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 30)->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 10)->nullable()->after('phone_number');
            }
            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable()->after('country');
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('city');
            }
            if (! Schema::hasColumn('users', 'postal')) {
                $table->string('postal', 20)->nullable()->after('gender');
            }
            if (! Schema::hasColumn('users', 'addresse')) {
                $table->string('addresse', 255)->nullable()->after('postal');
            }
            if (! Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('addresse');
            }
            if (! Schema::hasColumn('users', 'created_date')) {
                $table->timestamp('created_date')->nullable()->after('birth_date');
            }
            if (! Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('created_date');
            }
            if (! Schema::hasColumn('users', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('active');
            }
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('account_id');
            }
        });

        if (Schema::hasTable('role')) {
            if (! DB::table('role')->where('name', 'ROLE_ADMIN')->exists()) {
                DB::table('role')->insert([
                    'name' => 'ROLE_ADMIN',
                    'created_date' => now(),
                    'last_modified_date' => now(),
                ]);
            }

            if (! DB::table('role')->where('name', 'ROLE_USER')->exists()) {
                DB::table('role')->insert([
                    'name' => 'ROLE_USER',
                    'created_date' => now(),
                    'last_modified_date' => now(),
                ]);
            }
        }

        if (Schema::hasColumn('users', 'active')) {
            DB::table('users')->whereNull('active')->update(['active' => true]);
        }

        if (
            Schema::hasColumn('users', 'role_id') &&
            Schema::hasTable('role') &&
            DB::table('users')->where('email', 'admin@mamcash.com')->exists()
        ) {
            $adminRoleId = DB::table('role')->where('name', 'ROLE_ADMIN')->value('id');

            DB::table('users')
                ->where('email', 'admin@mamcash.com')
                ->update([
                    'active' => true,
                    'role_id' => $adminRoleId,
                ]);
        }
    }

    public function down(): void
    {
        // Keep data/schema rollback simple for production safety.
    }
};

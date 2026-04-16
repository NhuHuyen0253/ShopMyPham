<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'remember_token')) {
                $table->rememberToken()->nullable()->after('password');
            }

            if (!Schema::hasColumn('admins', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('admins', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('admins')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        DB::table('admins')
            ->whereNull('updated_at')
            ->update(['updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'remember_token')) {
                $table->dropColumn('remember_token');
            }

            if (Schema::hasColumn('admins', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('admins', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
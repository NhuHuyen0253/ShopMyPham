<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {

            // Tạo session_id
            if (!Schema::hasColumn('carts', 'session_id')) {
                $table->string('session_id')->nullable()->index()->after('user_id');
            }

            // Xóa cart_token
            if (Schema::hasColumn('carts', 'cart_token')) {
                $table->dropColumn('cart_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('cart_token')->nullable();
            $table->dropColumn('session_id');
        });
    }
};

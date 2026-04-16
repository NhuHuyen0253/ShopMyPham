<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_carrier')->nullable()->after('payment_method');   // đơn vị vc
            $table->string('tracking_code')->nullable()->after('shipping_carrier');    // mã vận đơn
            $table->integer('shipping_fee')->default(0)->after('tracking_code');       // phí ship
            $table->timestamp('shipped_at')->nullable()->after('shipping_fee');        // ngày gửi
            $table->text('shipping_note')->nullable()->after('shipped_at');            // ghi chú
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_carrier',
                'tracking_code',
                'shipping_fee',
                'shipped_at',
                'shipping_note',
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // thông tin nhận hàng
            $table->string('receiver_name', 120)->nullable()->after('total');
            $table->string('receiver_phone', 30)->nullable()->after('receiver_name');
            $table->string('receiver_addr', 255)->nullable()->after('receiver_phone');

            // phương thức thanh toán: 'cod', 'bank', ...
            $table->string('payment_method', 30)->nullable()->after('receiver_addr');

            // (tuỳ chọn) chuẩn hoá kiểu total nếu cần
            // $table->decimal('total', 12, 0)->change(); // nếu muốn lưu VND không lẻ
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['receiver_name', 'receiver_phone', 'receiver_addr', 'payment_method']);
        });
    }
};

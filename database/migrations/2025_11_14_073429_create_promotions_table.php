<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('promotions', function (Blueprint $table) {
        $table->id();
        $table->string('name');                    // Tên chương trình
        $table->string('code')->unique();         // Mã khuyến mãi (nhập khi đặt hàng)
        $table->enum('discount_type', ['percent', 'fixed']); // % hoặc tiền cố định
        $table->decimal('discount_value', 10, 2); // Giá trị giảm
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('min_order_value', 10, 2)->nullable();  // Đơn tối thiểu
        $table->decimal('max_discount_value', 10, 2)->nullable(); // Giảm tối đa
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('promotions');
}

};

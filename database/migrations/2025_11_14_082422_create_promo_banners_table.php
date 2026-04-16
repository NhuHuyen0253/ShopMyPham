<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('promo_banners', function (Blueprint $table) {
        $table->id();

        // Khóa ngoại trỏ tới bảng promotions
        $table->unsignedBigInteger('promotion_id')->nullable();

        $table->string('name'); // tên nội bộ để quản lý
        $table->string('headline'); // tiêu đề lớn (Big Sale...)
        $table->string('subheadline')->nullable(); // dòng phụ
        $table->text('description')->nullable();   // nội dung mô tả ngắn
        $table->string('discount_text')->nullable(); // ví dụ: "Giảm đến 50%"
        $table->string('button_text')->nullable();   // ví dụ: "Mua ngay"
        $table->string('button_link')->nullable();   // URL khi bấm nút
        $table->string('image_left_url')->nullable();  // ảnh trái
        $table->string('image_right_url')->nullable(); // ảnh phải
        $table->dateTime('start_at')->nullable();   // thời gian bắt đầu
        $table->dateTime('end_at')->nullable();     // thời gian kết thúc (đếm ngược)
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);  // sắp xếp nếu sau này có nhiều banner
        $table->timestamps();

        // Định nghĩa foreign key
        $table->foreign('promotion_id')
              ->references('id')
              ->on('promotions')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::dropIfExists('promo_banners');
}

};

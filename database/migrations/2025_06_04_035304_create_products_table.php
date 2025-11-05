<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price'); // lưu giá theo đơn vị VNĐ (int)
            $table->string('image')->nullable(); // đường dẫn ảnh sản phẩm
            $table->timestamps(); // created_at và updated_at
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

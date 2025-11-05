<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('cart_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('variant_id')->nullable();
      $table->unsignedInteger('quantity')->default(1);
      $table->json('meta')->nullable(); // size, color, options…
      $table->timestamps();

      $table->unique(['cart_id','product_id','variant_id']); // 1 dòng/biến thể
      $table->index(['product_id','variant_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('cart_items'); }
};

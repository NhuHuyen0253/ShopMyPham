<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('carts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
      $table->string('cart_token', 64)->nullable()->unique(); // cho guest
      $table->timestamps();
      $table->index(['user_id']);
      $table->index(['cart_token']);
    });
  }
  public function down(): void { Schema::dropIfExists('carts'); }
};

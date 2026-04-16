<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('messages', function (Blueprint $t) {
      $t->id();
      $t->string('name')->nullable();         // tên người chat (khách)
      $t->string('phone')->nullable();
      $t->text('content');                    // nội dung
      $t->boolean('from_admin')->default(0);  // 0: khách, 1: admin
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('messages'); }
};

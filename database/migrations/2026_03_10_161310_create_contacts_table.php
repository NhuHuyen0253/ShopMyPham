<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('contacts', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('email');

        $table->string('subject'); // tiêu đề câu hỏi
        $table->text('message');   // nội dung câu hỏi

        $table->string('status')->default('pending'); 
        // pending | replied (sau này admin trả lời)

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

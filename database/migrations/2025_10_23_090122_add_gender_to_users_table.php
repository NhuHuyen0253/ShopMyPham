<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('gender')->nullable();  // Thêm cột gender với kiểu dữ liệu string
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('gender');  // Nếu rollback, xóa cột gender
    });
}

};

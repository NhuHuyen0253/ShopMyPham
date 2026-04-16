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
    Schema::table('employees', function (Blueprint $table) {
        $table->date('dob');
        $table->enum('gender', ['Male', 'Female', 'Other']);
        $table->date('hire_date');
        $table->enum('status', ['Active', 'Resigned']);
        $table->string('avatar')->nullable();
    });
}

public function down()
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropColumn(['dob', 'gender', 'hire_date', 'status', 'avatar']);
    });
}

};

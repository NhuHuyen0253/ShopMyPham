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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_refunded')->default(false)->after('is_paid');
            $table->timestamp('refunded_at')->nullable()->after('is_refunded');
            $table->string('refund_note', 2000)->nullable()->after('refunded_at');
        });
    }

public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_refunded', 'refunded_at', 'refund_note']);
        });
    }
};

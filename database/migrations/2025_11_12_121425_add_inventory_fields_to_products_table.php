<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products','image_path')) $table->string('image_path')->nullable()->after('id');
            if (!Schema::hasColumn('products','supplier_id')) $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete()->after('image_path');
            if (!Schema::hasColumn('products','default_warehouse_id')) $table->foreignId('default_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete()->after('supplier_id');
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products','default_warehouse_id')) $table->dropConstrainedForeignId('default_warehouse_id');
            if (Schema::hasColumn('products','supplier_id')) $table->dropConstrainedForeignId('supplier_id');
            if (Schema::hasColumn('products','image_path')) $table->dropColumn('image_path');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Nếu bảng chưa có, có thể tạo luôn (bỏ khối này nếu bạn chắc chắn đã có bảng)
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $t) {
                $t->id();
                $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $t->string('path');              // <— cột lưu đường dẫn file
                $t->string('alt')->nullable();
                $t->unsignedInteger('sort_order')->default(0);
                $t->timestamps();
            });
            return;
        }

        Schema::table('product_images', function (Blueprint $t) {
            // Thêm 'path' nếu chưa có
            if (!Schema::hasColumn('product_images', 'path')) {
                $t->string('path');
            }
            // Thêm 'alt' nếu chưa có
            if (!Schema::hasColumn('product_images', 'alt')) {
                $t->string('alt')->nullable();
            }
            // Thêm 'sort_order' nếu chưa có
            if (!Schema::hasColumn('product_images', 'sort_order')) {
                $t->unsignedInteger('sort_order')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('product_images')) {
            Schema::table('product_images', function (Blueprint $t) {
                if (Schema::hasColumn('product_images', 'sort_order')) $t->dropColumn('sort_order');
                if (Schema::hasColumn('product_images', 'alt')) $t->dropColumn('alt');
                // Không xóa 'path' nếu các phần khác đang dùng; chỉ bỏ nếu bạn chắc chắn
                // if (Schema::hasColumn('product_images', 'path')) $t->dropColumn('path');
            });
        }
    }
};

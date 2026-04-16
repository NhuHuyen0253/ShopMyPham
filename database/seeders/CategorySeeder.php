<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class CategorySeeder extends Seeder
{
    public function run(): void
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate(); // Xóa toàn bộ dữ liệu, reset auto-increment
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
        //Face
        ['name' => 'Tẩy Trang Mặt', 'slug' => 'tay-trang-mat', 'type' => 'face'],
        ['name' => 'Sữa Rửa Mặt', 'slug' => 'sua-rua-mat', 'type' => 'face'],
        ['name' => 'Tẩy Tế Bào Chết Da Mặt', 'slug' => 'tay-te-bao-chet-da-mat', 'type' => 'face'],
        ['name' => 'Toner / Nước Cân Bằng Da', 'slug' => 'toner-nuoc-can-bang-da', 'type' => 'face'],
        ['name' => 'Đặc Trị', 'slug' => 'dac-tri', 'type' => 'face'],
        ['name' => 'Dưỡng Da', 'slug' => 'duong-am', 'type' => 'face'],
        ['name' => 'Dưỡng Môi', 'slug' => 'duong-moi', 'type' => 'face'],
        ['name' => 'Mặt Nạ', 'slug' => 'mat-na', 'type' => 'face'],
        ['name' => 'Chống Nắng Da Mặt', 'slug' => 'chong-nang-da-mat', 'type' => 'face'],
        ['name' => 'Dụng Cụ Chăm Sóc Da', 'slug' => 'dung-cu-cham-soc-da', 'type' => 'face'],

        //Hair
        ['name' => 'Dầu Gội', 'slug' => 'dau-goi', 'type' => 'hair'],
        ['name' => 'Dầu Xả', 'slug' => 'dau-xa', 'type' => 'hair'],
        ['name' => 'Ủ Tóc', 'slug' => 'u-toc', 'type' => 'hair'],
        ['name' => 'Serum Tóc', 'slug' => 'serum-toc', 'type' => 'hair'],
        ['name' => 'Dưỡng Tóc', 'slug' => 'duong-toc', 'type' => 'hair'],
        ['name' => 'Thuốc Nhuộm Tóc', 'slug' => 'thuoc-nhuom-toc', 'type' => 'hair'],
        ['name' => 'Tẩy Tế Bào Chết Da Đầu', 'slug' => 'tay-te-bao-chet-da-dau', 'type' => 'hair'],
        ['name' => 'Dụng Cụ Chăm Sóc Tóc', 'slug' => 'dung-cu-cham-soc-toc', 'type' => 'hair'],

        //Body
        ['name' => 'Sữa Tắm', 'slug' => 'sua-tam', 'type' => 'body'],
        ['name' => 'Sữa Dưỡng Thể', 'slug' => 'sua-duong-the', 'type' => 'body'],
        ['name' => 'Serum Dưỡng Thể', 'slug' => 'serum-duong-the', 'type' => 'body'],
        ['name' => 'Chống Nắng Body', 'slug' => 'chong-nang-body', 'type' => 'body'],
        ['name' => 'Tẩy Tế Bào Chết Body', 'slug' => 'tay-te-bao-chet-body', 'type' => 'body'],
        ['name' => 'Khử Mùi Cơ Thể', 'slug' => 'khu-mui-co-the', 'type' => 'body'],
        ['name' => 'Dụng Cụ Tắm', 'slug' => 'dung-cu-tam', 'type' => 'body'],
        ['name' => 'Tẩy Lông', 'slug' => 'tay-long', 'type' => 'body'],
        ['name' => 'DUng Dịch Vệ Sinh', 'slug' => 'dung-dich-ve-sinh', 'type' => 'body'],

        //Makeup
        ['name' => 'Kem Lót', 'slug' => 'kem-lot', 'type' => 'makeup'],
        ['name' => 'Kem Nền', 'slug' => 'kem-nen', 'type' => 'makeup'],
        ['name' => 'Phấn nước Cushion', 'slug' => 'phan-nuoc-cushion', 'type' => 'makeup'],
        ['name' => 'Che Khuyết Điểm', 'slug' => 'che-khuyet-diem', 'type' => 'makeup'],
        ['name' => 'Phấn Phủ', 'slug' => 'phan-phu', 'type' => 'makeup'],
        ['name' => 'Phấn Mắt', 'slug' => 'phan-mat', 'type' => 'makeup'],
        ['name' => 'Má Hồng', 'slug' => 'ma-hong', 'type' => 'makeup'],
        ['name' => 'Kẻ Mắt', 'slug' => 'ke-mat', 'type' => 'makeup'],
        ['name' => 'Kẻ Mày', 'slug' => 'ke-may', 'type' => 'makeup'],
        ['name' => 'Tạo Khối', 'slug' => 'tao-khoi', 'type' => 'makeup'],
        ['name' => 'Mascara', 'slug' => 'mascara', 'type' => 'makeup'],
        ['name' => 'Son', 'slug' => 'son', 'type' => 'makeup'],
        ['name' => 'Dụng Cụ Trang Điểm', 'slug' => 'dung-cu-trang-diem', 'type' => 'makeup'],
        ['name' => 'Tẩy trang mắt/môi', 'slug' => 'tay-trang-mat-moi', 'type' => 'makeup'],
        
    ];

    foreach ($categories as $item) {
        Category::create([
            'name' => $item['name'],
            'slug' => $item['slug'],
            'type' => $item['type'],
        ]);
        }
    }

}

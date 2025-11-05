<?php
namespace Database\Seeders;

use App\Models\Brands;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
         /*
           // Tắt kiểm tra khóa ngoại để có thể xóa dữ liệu an toàn
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Xóa toàn bộ dữ liệu bảng products
            DB::table('products')->delete();

            // Reset ID tự tăng về 1
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1;');

            // Bật lại kiểm tra khóa ngoại
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $products = [
            [
                'name' => 'Sữa Chống Nắng Anessa Dưỡng Da Kiềm Dầu 60ml (Bản Mới)',
                'description' => 'Perfect UV Sunscreen Skincare Milk N SPF50+ PA++++',
                'image' => 'anessa_60ml_chongnang.jpg',
                'price' => 420000,
                'category' => 'Chống nắng da mặt',
                'brand' => 'Anessa',
            ],
            [
                'name' => 'Sữa Rửa Mặt Simple Giúp Da Sạch Thoáng, Lành Tính 150ml',
                'description' => 'Lành tính, sạch thoáng cho da nhạy cảm',
                'image' => 'simple_suaruamat.jpg',
                'price' => 80000,
                'category' => 'Sữa Rửa Mặt',
                'brand' => 'Simple',
            ],
            [
                'name' => 'Nước Tẩy Trang L\'Oreal Tươi Mát Cho Da Dầu, Hỗn Hợp 400ml',
                'description' => 'Làm sạch lớp make-up',
                'image' => 'l`oreal_taytrang.jpg',
                'price' => 130000,
                'category' => 'Tẩy Trang Mặt',
                'brand' => "L 'Oreal",
            ],
            [
                'name' => 'Sữa rửa mặt Cetaphil Gentle Skin Cleanser 500ml',
                'description' => 'Làm sạch dịu nhẹ, không gây kích ứng, cân bằng độ pH, có thể dùng không cần rửa lại với nước.',
                'image' => '',
                'price' => 130000,
                'category' => 'Sữa Rửa Mặt',
                'brand' => 'Cetaphil',
            ],
            
            

        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();
            $brand = Brands::where('name', $data['brand'])->first();

            if (!$category || !$brand) {
                throw new \Exception("Thiếu category hoặc brand: {$data['category']} - {$data['brand']}");
            }

            Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'category_id' => $category->id,
                'brand_id' => $brand->id,
            ]);
        }
            */
    }
}

<?php

namespace Database\Seeders;

use App\Models\Brands;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            
        ];

        foreach ($brands as $brand) {
            $slug = Str::slug($brand['name']);
            $brand['slug'] = $slug;
            $brand['image'] = $slug . '.jpg'; // Thêm tên ảnh mặc định dạng slug.jpg

            Brands::updateOrCreate(['slug' => $slug], $brand);
        }
    }
}

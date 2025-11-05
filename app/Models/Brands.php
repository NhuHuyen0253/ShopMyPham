<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    protected $table = 'brands';
    protected $fillable = ['name', 'slug', 'image'];

    // Một thương hiệu có nhiều sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}

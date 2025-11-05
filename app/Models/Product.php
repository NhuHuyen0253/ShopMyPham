<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brands;

class Product extends Model
{
    use HasFactory;

  protected $fillable = [
    'name',
    'price',
    'quantity',
    'brand_id',
    'category_id',
    'is_hotdeal',
    'description',
    'usage_instructions'
];

    public function brand()
        {
            return $this->belongsTo(Brands::class, 'brand_id');
        }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_product')
                    ->withPivot('quantity');
    }

    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

}
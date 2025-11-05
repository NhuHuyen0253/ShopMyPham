<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'file_name', 'path', 'alt', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Trả về URL đầy đủ để show ảnh
    public function getUrlAttribute()
    {
        $base = trim($this->path ?: '', '/');
        return asset(($base ? $base.'/' : '').$this->file_name);
    }
}

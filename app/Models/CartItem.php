<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
    ];

    protected $casts = [
        'cart_id'    => 'integer',
        'product_id' => 'integer',
        'quantity'   => 'integer',
        'price'      => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFinalPriceAttribute(): int
    {
        $price = (int) $this->price;

        $isHotdeal = (bool) ($this->product->is_hotdeal ?? false);
        $discountPercent = (int) ($this->product->discount_percent ?? 0);

        if ($isHotdeal && $discountPercent > 0) {
            return (int) floor($price * (100 - $discountPercent) / 100);
        }

        return $price;
    }

    public function getSubtotalAttribute(): int
    {
        return $this->final_price * (int) $this->quantity;
    }
}
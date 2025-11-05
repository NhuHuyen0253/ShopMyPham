<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id','product_id','variant_id','quantity','meta'];

    protected $casts = [
        'meta' => 'array',
        'quantity' => 'integer',
    ];

    public function cart(): BelongsTo {
        return $this->belongsTo(Cart::class);
    }
}

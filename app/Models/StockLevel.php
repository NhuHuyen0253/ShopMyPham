<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    protected $table = 'stock_levels';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'reorder_point',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reorder_point' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
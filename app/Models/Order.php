<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'stock_deducted',
        'total',

        'receiver_name',
        'receiver_phone',
        'receiver_addr',

        'payment_method',
        'is_paid',
        'admin_note',

        'discount',
        'promotion_code',

        'is_refunded',
        'refunded_at',
        'refund_note',

        'shipping_carrier',
        'shipping_service',
        'tracking_code',
        'shipping_fee',
        'shipping_note',
        'shipped_at',

        'to_district_id',
        'to_ward_code',
    ];

    protected $casts = [
        'total'           => 'decimal:2',
        'is_paid'         => 'boolean',
        'stock_deducted'  => 'boolean',
        'discount'        => 'integer',
        'shipping_fee'    => 'integer',
        'is_refunded'     => 'boolean',
        'refunded_at'     => 'datetime',
        'shipped_at'      => 'datetime',
        'to_district_id'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
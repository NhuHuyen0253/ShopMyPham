<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'ingredients',
        'usage_instructions',
        'price',
        'original_price',
        'discount_percent',
        'quantity',
        'reserved_quantity',
        'image',
        'brand_id',
        'category_id',
        'group_code',
        'capacity',
        'is_hotdeal',
        'supplier_id',
        'default_warehouse_id',
    ];

    protected $casts = [
        'is_hotdeal'         => 'boolean',
        'price'              => 'integer',
        'original_price'     => 'integer',
        'discount_percent'   => 'integer',
        'quantity'           => 'integer',
        'reserved_quantity'  => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function defaultWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product', 'product_id', 'promotion_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        $img = trim((string) $this->image, '/');
        if (!$img) {
            return null;
        }

        $img = str_replace('\\', '/', $img);

        if (str_starts_with($img, 'products/')) {
            return asset('storage/' . $img);
        }

        if (str_starts_with($img, 'images/')) {
            return asset($img);
        }

        if (!str_contains($img, '/')) {
            return asset('storage/products/' . $img);
        }

        return asset($img);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->quantity;
    }

    public function getAvailableQuantityAttribute(): int
    {
        $quantity = (int) ($this->quantity ?? 0);
        $reserved = (int) ($this->reserved_quantity ?? 0);

        return max(0, $quantity - $reserved);
    }

    public function reserveStock(int $qty): void
    {
        $qty = max(0, $qty);

        if ($qty < 1) {
            return;
        }

        $available = max(0, (int) $this->quantity - (int) $this->reserved_quantity);

        if ($available < $qty) {
            throw new \RuntimeException('Số lượng hàng còn không đủ.');
        }

        $this->reserved_quantity = (int) $this->reserved_quantity + $qty;
        $this->save();
    }

    public function releaseReservedStock(int $qty): void
    {
        $qty = max(0, $qty);

        if ($qty < 1) {
            return;
        }

        $currentReserved = (int) ($this->reserved_quantity ?? 0);
        $this->reserved_quantity = max(0, $currentReserved - $qty);
        $this->save();
    }
}
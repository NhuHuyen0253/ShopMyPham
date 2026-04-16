<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'file_name',
        'path',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* URL gallery – hỗ trợ 2 kiểu lưu */
    public function getUrlAttribute(): ?string
    {
        $p = trim((string)$this->path, '/');
        $f = trim((string)$this->file_name, '/');
        if (!$p || !$f) return null;

        if (str_starts_with($p, 'product_images/')) {
            // ảnh trong storage/app/public
            return asset('storage/'.$p.'/'.$f);
        }
        // fallback: public/images/...
        return asset($p.'/'.$f);
    }

    /* Xoá file vật lý */
    public function deletePhysicalFile(): void
    {
        $p = trim((string)$this->path, '/');
        $f = trim((string)$this->file_name, '/');
        if (!$p || !$f) return;

        if (str_starts_with($p, 'product_images/')) {
            Storage::disk('public')->delete($p.'/'.$f);
        } else {
            @unlink(public_path($p.'/'.$f));
        }
    }
}

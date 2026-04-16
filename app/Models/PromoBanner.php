<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    protected $fillable = [
        'promotion_id',
        'name',
        'image',
        'headline',
        'subheadline',
        'description',
        'discount_text',
        'button_text',
        'button_link',
        'image_left_url',
        'image_right_url',
        'start_at',
        'end_at',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_at'   => 'datetime',
        'end_at'     => 'datetime',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
}
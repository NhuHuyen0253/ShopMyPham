<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'product',
        'serial_numbers',
        'note',
    ];
}

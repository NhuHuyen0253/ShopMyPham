<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'session_id',
        'content',
        'from_admin',
    ];

    protected $casts = [
        'from_admin' => 'boolean',
    ];
}
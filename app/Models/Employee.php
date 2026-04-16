<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Authenticatable
{
    use HasFactory;

     protected $table = 'employees';

    protected $fillable = [
        'name',
        'dob',
        'gender',
        'phone',
        'email',
        'position',
        'hire_date',
        'status',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
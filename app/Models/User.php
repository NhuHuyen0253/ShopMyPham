<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;  // Đảm bảo kế thừa từ Authenticatable
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;  // Đảm bảo sử dụng trait Notifiable

    // Mối quan hệ giữa User và Cart
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'gender', 'dob'
    ];
}

<?php
namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class CartIdentity {
    public static function token(): string {
        $token = request()->cookie('cart_token');
        if (!$token) {
            $token = Str::uuid()->toString();
            Cookie::queue('cart_token', $token, 60*24*30); // 30 ngày
        }
        return $token;
    }
}

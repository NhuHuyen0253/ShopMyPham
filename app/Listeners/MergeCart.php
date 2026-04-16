<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MergeCart
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Lấy token giỏ của guest
        $token = Session::get('cart_token');
        if (!$token) {
            return;
        }

        // Tìm giỏ hàng guest theo cart_token
        $guestCart = Cart::with('items')->where('cart_token', $token)->first();
        if (!$guestCart) {
            return;
        }

        // Lấy (hoặc tạo) giỏ của user vừa login
        $userId   = $event->user->id ?? Auth::id();
        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            // Kiểm tra coi userCart đã có sản phẩm/variant này chưa
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existing) {
                // Cộng dồn số lượng
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                // Tạo item mới cho giỏ user
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                    'meta'       => $item->meta,
                ]);
            }
        }

        // Xoá giỏ guest + token sau khi merge xong
        $guestCart->items()->delete();
        $guestCart->delete();
        Session::forget('cart_token');
    }
}

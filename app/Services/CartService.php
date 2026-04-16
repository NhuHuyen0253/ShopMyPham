<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /** Lấy hoặc tạo giỏ cho người dùng / khách */
    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        // Guest → dùng cart_token
        $token = Session::get('cart_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::put('cart_token', $token);
        }

        return Cart::firstOrCreate(['cart_token' => $token]);
    }

    /** Merge guest cart vào user cart khi login */
    public function mergeGuestCartToUser()
    {
        if (!Auth::check()) return;

        $token = Session::get('cart_token');
        if (!$token) return;

        $guest = Cart::where('cart_token', $token)->with('items')->first();
        if (!$guest) return;

        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Merge từng item
        foreach ($guest->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                $userCart->items()->create($item->only([
                    'product_id', 'variant_id', 'quantity', 'meta'
                ]));
            }
        }

        // Xoá giỏ guest
        $guest->items()->delete();
        $guest->delete();
        Session::forget('cart_token');
    }
}

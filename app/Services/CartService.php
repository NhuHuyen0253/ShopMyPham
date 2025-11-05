<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\Eloquent\Builder;

class CartService
{
    const COOKIE = 'cart_token';
    const COOKIE_MINUTES = 60 * 24 * 60; // ~60 ngày

    public function currentCart(Request $request): Cart
    {
        $user = $request->user();
        $token = $request->cookie(self::COOKIE);

        // 1) Ưu tiên user
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id], []);
            // Nếu có token khác và có giỏ guest -> merge
            if ($token) {
                $guest = Cart::where('cart_token', $token)->first();
                if ($guest && $guest->id !== $cart->id) {
                    $this->merge($guest, $cart);
                    $guest->delete();
                }
            }
            // Set/refresh cookie để giữ state multi-device nếu cần
            Cookie::queue(self::COOKIE, $cart->cart_token ?? Str::ulid(), self::COOKIE_MINUTES, httpOnly: false);
            if (!$cart->cart_token) { $cart->update(['cart_token' => $request->cookie(self::COOKIE)]); }
            return $cart;
        }

        // 2) Guest
        if (!$token) {
            $token = Str::ulid();
            Cookie::queue(self::COOKIE, $token, self::COOKIE_MINUTES, httpOnly: false);
        }
        return Cart::firstOrCreate(['cart_token' => $token], []);
    }

    public function add(Request $request, array $payload): Cart
    {
        $cart = $this->currentCart($request);

        $productId = (int) ($payload['product_id'] ?? 0);
        $variantId = $payload['variant_id'] ?? null;
        $qty = max(1, (int) ($payload['quantity'] ?? 1));
        $meta = $payload['meta'] ?? null;

        $item = $cart->items()->where(function(Builder $q) use ($productId, $variantId) {
            $q->where('product_id', $productId)
              ->whereNull('variant_id');
            if ($variantId) {
                $q->orWhere(function($q2) use($productId, $variantId){
                    $q2->where('product_id', $productId)->where('variant_id', $variantId);
                });
            }
        })->first();

        if ($item) {
            $item->increment('quantity', $qty);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $qty,
                'meta'       => $meta,
            ]);
        }
        $cart->refresh();
        return $cart;
    }

    public function updateQuantity(Request $request, int $itemId, int $quantity): Cart
    {
        $cart = $this->currentCart($request);
        $item = $cart->items()->findOrFail($itemId);
        if ($quantity <= 0) { $item->delete(); }
        else { $item->update(['quantity' => $quantity]); }
        $cart->refresh();
        return $cart;
    }

    public function removeItem(Request $request, int $itemId): Cart
    {
        $cart = $this->currentCart($request);
        $cart->items()->whereKey($itemId)->delete();
        $cart->refresh();
        return $cart;
    }

    public function clear(Request $request): void
    {
        $cart = $this->currentCart($request);
        $cart->items()->delete();
    }

    public function count(Request $request): int
    {
        return $this->currentCart($request)->totalCount();
    }

    /** Merge guest -> user (gộp quantity nếu trùng product/variant) */
    public function merge(Cart $from, Cart $to): void
    {
        foreach ($from->items as $g) {
            $t = $to->items()
                ->where('product_id', $g->product_id)
                ->where('variant_id', $g->variant_id)
                ->first();
            if ($t) {
                $t->increment('quantity', $g->quantity);
            } else {
                $to->items()->create([
                    'product_id' => $g->product_id,
                    'variant_id' => $g->variant_id,
                    'quantity'   => $g->quantity,
                    'meta'       => $g->meta,
                ]);
            }
        }
        $to->refresh();
    }
}

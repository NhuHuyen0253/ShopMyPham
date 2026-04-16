<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $service)
    {
        $this->cartService = $service;
    }

    private function cartKey(): string
    {
        if (!Auth::check()) {
            return 'cart_guest';
        }

        return 'cart_user_' . Auth::id();
    }

    private function promotionKey(): string
    {
        if (!Auth::check()) {
            return 'promotion_guest';
        }

        return 'promotion_user_' . Auth::id();
    }

    private function checkoutKey(): string
    {
        if (!Auth::check()) {
            return 'checkout_cart_guest';
        }

        return 'checkout_cart_user_' . Auth::id();
    }

    public function index()
    {
        return $this->viewCart();
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng.',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $productId = (int) $validated['product_id'];
        $qty       = (int) ($validated['quantity'] ?? 1);

        try {
            $result = DB::transaction(function () use ($productId, $qty) {
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    return [
                        'status' => 404,
                        'data'   => [
                            'success' => false,
                            'message' => 'Sản phẩm không tồn tại.',
                        ],
                    ];
                }

                $imageUrl = $this->resolveProductImageUrl($product);

                $cartKey = $this->cartKey();
                $cart = session()->get($cartKey, []);

                $currentQtyInCart = (int) ($cart[$productId]['quantity'] ?? 0);

                $available = max(
                    0,
                    (int) $product->quantity - (int) $product->reserved_quantity + $currentQtyInCart
                );

                if ($available < $qty) {
                    return [
                        'status' => 422,
                        'data'   => [
                            'success' => false,
                            'message' => 'Số lượng hàng còn không đủ.',
                        ],
                    ];
                }

                $product->reserved_quantity = (int) $product->reserved_quantity + $qty;
                $product->save();

                if (!isset($cart[$productId])) {
                    $cart[$productId] = [
                        'product_id'       => $productId,
                        'name'             => $product->name,
                        'price'            => (int) ($product->price ?? 0),
                        'is_hotdeal'       => (bool) ($product->is_hotdeal ?? false),
                        'discount_percent' => (int) ($product->discount_percent ?? 0),
                        'quantity'         => 0,
                        'image_url'        => $imageUrl,
                    ];
                } else {
                    $cart[$productId]['name']             = $product->name;
                    $cart[$productId]['price']            = (int) ($product->price ?? 0);
                    $cart[$productId]['is_hotdeal']       = (bool) ($product->is_hotdeal ?? false);
                    $cart[$productId]['discount_percent'] = (int) ($product->discount_percent ?? 0);
                    $cart[$productId]['image_url']        = $imageUrl;
                }

                $cart[$productId]['quantity'] = $currentQtyInCart + $qty;

                session()->put($cartKey, $cart);
                $this->syncCartToDatabase();

                $count = array_sum(array_column($cart, 'quantity'));

                return [
                    'status' => 200,
                    'data'   => [
                        'success' => true,
                        'message' => 'Đã thêm vào giỏ hàng.',
                        'count'   => $count,
                        'item'    => $cart[$productId],
                    ],
                ];
            });

            return response()->json($result['data'], $result['status']);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Không thể thêm vào giỏ hàng.',
            ], 500);
        }
    }

    public function count(Request $request)
    {
        $cartKey = $this->cartKey();
        $cart = session()->get($cartKey, []);
        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $count]);
    }

    public function updateQty(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:999',
            'qty'      => 'nullable|integer|min:1|max:999',
        ]);

        $newQty = (int) ($data['quantity'] ?? $data['qty'] ?? 1);
        if ($newQty < 1) {
            $newQty = 1;
        }

        try {
            $result = DB::transaction(function () use ($id, $newQty) {
                $cartKey = $this->cartKey();
                $cart = session($cartKey, []);

                if (!isset($cart[$id])) {
                    return [
                        'status' => 404,
                        'data'   => [
                            'ok'      => false,
                            'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
                        ],
                    ];
                }

                $product = Product::lockForUpdate()->find($id);
                if (!$product) {
                    return [
                        'status' => 404,
                        'data'   => [
                            'ok'      => false,
                            'message' => 'Sản phẩm không tồn tại.',
                        ],
                    ];
                }

                $oldQty = (int) ($cart[$id]['quantity'] ?? 0);
                $diff   = $newQty - $oldQty;

                if ($diff > 0) {
                    $available = max(
                        0,
                        (int) $product->quantity - (int) $product->reserved_quantity + $oldQty
                    );

                    if ($available < $newQty) {
                        return [
                            'status' => 422,
                            'data'   => [
                                'ok'      => false,
                                'message' => 'Số lượng hàng còn không đủ.',
                            ],
                        ];
                    }

                    $product->reserved_quantity = (int) $product->reserved_quantity + $diff;
                    $product->save();
                } elseif ($diff < 0) {
                    $release = abs($diff);
                    $product->reserved_quantity = max(
                        0,
                        (int) $product->reserved_quantity - $release
                    );
                    $product->save();
                }

                $cart[$id]['name']             = $product->name;
                $cart[$id]['price']            = (int) ($product->price ?? 0);
                $cart[$id]['is_hotdeal']       = (bool) ($product->is_hotdeal ?? false);
                $cart[$id]['discount_percent'] = (int) ($product->discount_percent ?? 0);
                $cart[$id]['quantity']         = $newQty;
                $cart[$id]['image_url']        = $this->resolveProductImageUrl($product);

                session([$cartKey => $cart]);
                $this->syncCartToDatabase();

                $totals = $this->totals($cart);

                $currentPrice = $this->finalPrice($cart[$id]);
                $lineSubtotal = $currentPrice * (int) $cart[$id]['quantity'];

                return [
                    'status' => 200,
                    'data'   => [
                        'ok'       => true,
                        'message'  => 'Đã cập nhật số lượng.',
                        'qty'      => (int) $cart[$id]['quantity'],
                        'price'    => $this->money($currentPrice),
                        'subtotal' => $this->money($lineSubtotal),
                        'totals'   => [
                            'subtotal' => $this->money($totals['subtotal']),
                            'discount' => $this->money($totals['discount']),
                            'total'    => $this->money($totals['total']),
                            'count'    => $totals['count'],
                        ],
                    ],
                ];
            });

            return response()->json($result['data'], $result['status']);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage() ?: 'Không thể cập nhật số lượng.',
            ], 500);
        }
    }

    public function remove(Request $request, $id)
    {
        $removeAll = filter_var($request->input('remove_all', false), FILTER_VALIDATE_BOOLEAN);

        try {
            $result = DB::transaction(function () use ($id, $removeAll) {
                $cartKey = $this->cartKey();
                $cart = session($cartKey, []);

                if (!isset($cart[$id])) {
                    return [
                        'status' => 404,
                        'data'   => [
                            'success' => false,
                            'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
                        ],
                    ];
                }

                $product = Product::lockForUpdate()->find($id);

                $currentQty = (int) ($cart[$id]['quantity'] ?? 0);
                $releaseQty = 0;
                $message    = 'Đã xóa khỏi giỏ.';

                if ($removeAll || $currentQty <= 1) {
                    $releaseQty = $currentQty;
                    unset($cart[$id]);
                    $message = 'Đã xóa khỏi giỏ.';
                } else {
                    $cart[$id]['quantity'] = $currentQty - 1;
                    $releaseQty = 1;
                    $message = 'Đã giảm số lượng.';
                }

                if ($product && $releaseQty > 0) {
                    $product->reserved_quantity = max(
                        0,
                        (int) $product->reserved_quantity - $releaseQty
                    );
                    $product->save();
                }

                session([$cartKey => $cart]);
                $this->syncCartToDatabase();

                $totals = $this->totals($cart);

                return [
                    'status' => 200,
                    'data'   => [
                        'success' => true,
                        'message' => $message,
                        'count'   => $totals['count'],
                        'totals'  => [
                            'subtotal' => $this->money($totals['subtotal']),
                            'discount' => $this->money($totals['discount']),
                            'total'    => $this->money($totals['total']),
                            'count'    => $totals['count'],
                        ],
                    ],
                ];
            });

            return response()->json($result['data'], $result['status']);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Không thể cập nhật giỏ hàng.',
            ], 500);
        }
    }

    public function viewCart()
    {
        $this->loadCartFromDatabase();
        $cartKey = $this->cartKey();
        $promotionKey = $this->promotionKey();

        $cart = session()->get($cartKey, []);

        foreach ($cart as $productId => &$item) {
            $product = Product::find($productId);

            if ($product) {
                $item['name']             = $product->name;
                $item['price']            = (int) ($product->price ?? 0);
                $item['is_hotdeal']       = (bool) ($product->is_hotdeal ?? false);
                $item['discount_percent'] = (int) ($product->discount_percent ?? 0);
                $item['image_url']        = $this->resolveProductImageUrl($product);

                $available = max(
                    0,
                    (int) $product->quantity - (int) $product->reserved_quantity + (int) ($item['quantity'] ?? 0)
                );
                $item['available_quantity'] = $available;
            } else {
                if (!array_key_exists('image_url', $item)) {
                    $item['image_url'] = null;
                }
                if (!array_key_exists('is_hotdeal', $item)) {
                    $item['is_hotdeal'] = false;
                }
                if (!array_key_exists('discount_percent', $item)) {
                    $item['discount_percent'] = 0;
                }
                $item['available_quantity'] = 0;
            }
        }
        unset($item);

        session()->put($cartKey, $cart);

        $totals = $this->totals($cart);
        $promotion = session($promotionKey);

        return view('Cart', compact('cart', 'totals', 'promotion'));
    }

    private function resolveProductImageUrl($product): ?string
    {
        if (empty($product->image)) {
            return asset('images/product/no-image.png');
        }

        $file = trim(str_replace('\\', '/', $product->image), '/');

        if (is_file(public_path('images/product/' . $file))) {
            return asset('images/product/' . rawurlencode($file));
        }

        if (is_file(public_path($file))) {
            return asset($file);
        }

        return asset('images/product/no-image.png');
    }

    public function checkoutFromCart(Request $request)
    {
        $selected = array_filter(explode(',', $request->input('selected_ids', '')));

        if (empty($selected)) {
            return back()->with('error', 'Vui lòng chọn sản phẩm.');
        }

        $cartKey = $this->cartKey();
        $checkoutKey = $this->checkoutKey();

        $cart = session($cartKey, []);
        $items = [];

        foreach ($selected as $pid) {
            if (isset($cart[$pid])) {
                $items[] = [
                    'product_id' => (int) $pid,
                    'quantity'   => (int) $cart[$pid]['quantity'],
                ];
            }
        }

        if (empty($items)) {
            return back()->with('error', 'Không có sản phẩm hợp lệ.');
        }

        session([$checkoutKey => $items]);
        session()->save();

        return redirect()->route('order.confirm');
    }

    private function finalPrice(array $item): int
    {
        $price = (int) ($item['price'] ?? 0);
        $isHotdeal = (bool) ($item['is_hotdeal'] ?? false);
        $discountPercent = (int) ($item['discount_percent'] ?? 0);

        if ($isHotdeal && $discountPercent > 0) {
            return (int) floor($price * (100 - $discountPercent) / 100);
        }

        return $price;
    }

    private function totals(array $cart): array
    {
        $subtotal = 0;
        $discount = 0;
        $count = 0;

        foreach ($cart as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (int) ($item['price'] ?? 0);
            $final = $this->finalPrice($item);

            $subtotal += $final * $qty;
            $discount += max(0, ($price - $final) * $qty);
            $count += $qty;
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total'    => $subtotal,
            'count'    => $count,
        ];
    }

    private function money(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }

    private function syncCartToDatabase(): void
    {
        if (!Auth::check()) {
            return;
        }

        DB::transaction(function () {
            $cartKey = $this->cartKey();
            $sessionCart = session()->get($cartKey, []);

            $cart = \App\Models\Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);

            $cart->items()->delete();

            foreach ($sessionCart as $item) {
                $productId = (int) ($item['product_id'] ?? 0);

                if ($productId <= 0) {
                    continue;
                }

                $cart->items()->create([
                    'product_id' => $productId,
                    'product_name' => $item['name'] ?? '',
                    'quantity'   => max(1, (int) ($item['quantity'] ?? 1)),
                    'price'      => (int) ($item['price'] ?? 0), // lưu giá gốc
                ]);
            }
        });
    }
    private function loadCartFromDatabase(): void
    {
        if (!Auth::check()) {
            return;
        }

        $cartKey = $this->cartKey();

        // Nếu session đã có giỏ rồi thì không cần load lại
        if (!empty(session()->get($cartKey, []))) {
            return;
        }

        $cartModel = Cart::with('items')->where('user_id', Auth::id())->first();

        if (!$cartModel || $cartModel->items->isEmpty()) {
            return;
        }

        $sessionCart = [];

        foreach ($cartModel->items as $item) {
            $product = Product::find($item->product_id);

            $sessionCart[$item->product_id] = [
                'product_id'       => (int) $item->product_id,
                'name'             => $product->name ?? $item->product_name ?? '',
                'price'            => (int) ($product->price ?? $item->price ?? 0),
                'is_hotdeal'       => (bool) ($product->is_hotdeal ?? false),
                'discount_percent' => (int) ($product->discount_percent ?? 0),
                'quantity'         => max(1, (int) $item->quantity),
                'image_url'        => $product ? $this->resolveProductImageUrl($product) : asset('images/product/no-image.png'),
            ];
        }

        session()->put($cartKey, $sessionCart);
    }
}
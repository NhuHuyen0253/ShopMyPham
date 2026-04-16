<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private function cartKey(): string
    {
        if (!Auth::check()) {
            return 'cart_guest';
        }

        return 'cart_user_' . Auth::id();
    }

    private function checkoutKey(): string
    {
        if (!Auth::check()) {
            return 'checkout_cart_guest';
        }

        return 'checkout_cart_user_' . Auth::id();
    }

    private function buyNowKey(): string
    {
        if (!Auth::check()) {
            return 'buynow_guest';
        }

        return 'buynow_user_' . Auth::id();
    }

    public function buynow(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để mua hàng.');
        }

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $oldBn = session($this->buyNowKey());

                if ($oldBn && !empty($oldBn['product_id']) && !empty($oldBn['quantity'])) {
                    $oldProduct = Product::lockForUpdate()->find((int) $oldBn['product_id']);
                    if ($oldProduct) {
                        $oldProduct->reserved_quantity = max(
                            0,
                            (int) $oldProduct->reserved_quantity - (int) $oldBn['quantity']
                        );
                        $oldProduct->save();
                    }
                }

                session()->forget($this->checkoutKey());

                $product = Product::lockForUpdate()->findOrFail((int) $data['product_id']);
                $qty = (int) $data['quantity'];

                $available = max(0, (int) $product->quantity - (int) $product->reserved_quantity);

                if ($available < $qty) {
                    throw new \Exception('Số lượng hàng còn không đủ.');
                }

                $product->reserved_quantity = (int) $product->reserved_quantity + $qty;
                $product->save();

                session([
                    $this->buyNowKey() => [
                        'product_id' => (int) $data['product_id'],
                        'quantity'   => $qty,
                    ],
                ]);
            });

            return redirect()->route('order.confirm');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Không thể mua ngay.');
        }
    }

    public function confirm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $cartItems = session($this->checkoutKey());

        if (is_array($cartItems) && count($cartItems) > 0) {
            $items = [];
            $sum = 0;

            foreach ($cartItems as $it) {
                $product = Product::find($it['product_id']);
                if (!$product) {
                    continue;
                }

                $qty = max(1, (int) $it['quantity']);

                $reservedEnough = (int) ($product->reserved_quantity ?? 0) >= $qty;
                if (!$reservedEnough) {
                    continue;
                }

                $unit = $this->finalPrice($product);
                $line = $unit * $qty;

                $items[] = [
                    'product' => $product,
                    'qty'     => $qty,
                    'unit'    => $unit,
                    'line'    => $line,
                ];

                $sum += $line;
            }

            if (empty($items)) {
                return redirect('/cart')->with('error', 'Không có sản phẩm hợp lệ để thanh toán.');
            }

            $discount = 0;
            $defaultShippingFee = 30000;
            $grandTotal = max(0, $sum - $discount + $defaultShippingFee);

            return view('orders.confirm', compact('items', 'sum', 'discount', 'grandTotal', 'defaultShippingFee'));
        }

        $bn = session($this->buyNowKey());

        if (!$bn) {
            return redirect('/cart')->with('error', 'Không có dữ liệu thanh toán.');
        }

        $product = Product::findOrFail($bn['product_id']);
        $qty     = max(1, (int) $bn['quantity']);

        if ((int) ($product->reserved_quantity ?? 0) < $qty) {
            return redirect()->route('product.show', $product->id)
                ->with('error', 'Số lượng hàng còn không đủ.');
        }

        $unit = $this->toIntPrice($this->finalPrice($product));
        $total = $unit * $qty;
        $defaultShippingFee = 30000;

        $items = [[
            'product' => $product,
            'qty'     => $qty,
            'unit'    => $unit,
            'line'    => $total,
        ]];

        $grandTotal = $total + $defaultShippingFee;

        return view('orders.confirm', compact('items', 'grandTotal', 'defaultShippingFee'));
    }

    public function checkoutFromCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $oldBn = session($this->buyNowKey());
        if ($oldBn && !empty($oldBn['product_id']) && !empty($oldBn['quantity'])) {
            DB::transaction(function () use ($oldBn) {
                $oldProduct = Product::lockForUpdate()->find((int) $oldBn['product_id']);
                if ($oldProduct) {
                    $oldProduct->reserved_quantity = max(
                        0,
                        (int) $oldProduct->reserved_quantity - (int) $oldBn['quantity']
                    );
                    $oldProduct->save();
                }
                session()->forget($this->buyNowKey());
            });
        } else {
            session()->forget($this->buyNowKey());
        }

        $cart = session($this->cartKey(), []);
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống.');
        }

        $selected = array_filter(explode(',', (string) $request->input('selected_ids', '')));
        $selected = array_values(array_unique(array_map('intval', $selected)));

        if (empty($selected)) {
            $selected = array_map('intval', array_keys($cart));
        }

        $items = [];
        foreach ($selected as $pid) {
            if (isset($cart[$pid])) {
                $qty = (int) ($cart[$pid]['quantity'] ?? 1);
                if ($qty < 1) {
                    $qty = 1;
                }

                $items[] = [
                    'product_id' => (int) $pid,
                    'quantity'   => $qty,
                ];
            }
        }

        if (empty($items)) {
            return redirect('/cart')->with('error', 'Không có sản phẩm hợp lệ.');
        }

        session([$this->checkoutKey() => $items]);
        session()->save();

        return redirect()->route('order.confirm');
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $data = $request->validate([
            'fullname'          => ['required', 'string', 'max:120'],
            'phone'             => ['required', 'string', 'max:30'],
            'address'           => ['required', 'string', 'max:255'],
            'payment_method'    => ['required', 'in:cod,bank,vnpay'],
            'quantity'          => ['nullable', 'integer', 'min:1'],
            'shipping_fee'      => ['nullable', 'integer', 'min:0'],
            'shipping_provider' => ['nullable', 'string', 'max:100'],
            'shipping_service'  => ['nullable', 'string', 'max:100'],
        ]);

        $cartItems = session($this->checkoutKey());
        $bn        = session($this->buyNowKey());

        if ((!is_array($cartItems) || count($cartItems) === 0) && !$bn) {
            return redirect('/cart')->with('error', 'Không có dữ liệu thanh toán.');
        }

        DB::beginTransaction();

        try {
            $shippingFee = (int) ($data['shipping_fee'] ?? 30000);
            $shippingCarrier = $data['shipping_provider'] ?? 'GHN';
            $shippingService = $data['shipping_service'] ?? 'Giao hàng tiêu chuẩn';

            $order = Order::create([
                'user_id'         => Auth::id(),
                'status'          => ($data['payment_method'] === 'bank' || $data['payment_method'] === 'vnpay')
                    ? 'awaiting_payment'
                    : 'pending',
                'stock_deducted'  => 0,
                'total'           => 0,
                'shipping_fee'    => $shippingFee,
                'shipping_carrier'=> $shippingCarrier,
                'shipping_service'=> $shippingService,
                'receiver_name'   => $data['fullname'],
                'receiver_phone'  => $data['phone'],
                'receiver_addr'   => $data['address'],
                'payment_method'  => $data['payment_method'],
                'is_paid'         => 0,
            ]);

            $subtotal = 0;

            if (is_array($cartItems) && count($cartItems) > 0) {
                foreach ($cartItems as $it) {
                    $product = Product::lockForUpdate()->findOrFail($it['product_id']);
                    $qty     = max(1, (int) $it['quantity']);

                    if ((int) $product->reserved_quantity < $qty) {
                        throw new \Exception("Số lượng hàng còn không đủ cho sản phẩm {$product->name}.");
                    }

                    if ((int) $product->quantity < $qty) {
                        throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho.");
                    }

                    $price = $this->finalPrice($product);

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'price'      => $price,
                    ]);

                    $product->quantity = (int) $product->quantity - $qty;
                    $product->reserved_quantity = max(0, (int) $product->reserved_quantity - $qty);
                    $product->save();

                    $subtotal += $price * $qty;
                }

                $cart = session($this->cartKey(), []);
                foreach ($cartItems as $it) {
                    $pid = (int) $it['product_id'];
                    if (isset($cart[$pid])) {
                        unset($cart[$pid]);
                    }
                }

                session([$this->cartKey() => $cart]);
                session()->forget($this->checkoutKey());
            } else {
                $product = Product::lockForUpdate()->findOrFail($bn['product_id']);
                $qty     = max(1, (int) ($data['quantity'] ?? $bn['quantity'] ?? 1));

                if ((int) $product->reserved_quantity < $qty) {
                    throw new \Exception("Số lượng hàng còn không đủ cho sản phẩm {$product->name}.");
                }

                if ((int) $product->quantity < $qty) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho.");
                }

                $price = $this->finalPrice($product);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                ]);

                $product->quantity = (int) $product->quantity - $qty;
                $product->reserved_quantity = max(0, (int) $product->reserved_quantity - $qty);
                $product->save();

                $subtotal += $price * $qty;

                session()->forget($this->buyNowKey());
            }

            $grandTotal = $subtotal + $shippingFee;

            $order->update([
                'total'          => $grandTotal,
                'stock_deducted' => 1,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Không thể đặt hàng. Vui lòng thử lại.');
        }

        if ($data['payment_method'] === 'vnpay') {
            return redirect()->route('vnpay.create', ['order' => $order->id]);
        }

        return redirect()->route('order.notice', ['id' => $order->id]);
    }

    public function showorder($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        if ($order->user_id && (!Auth::check() || Auth::id() !== (int) $order->user_id)) {
            return redirect('/')->with('error', 'Bạn không có quyền xem đơn này.');
        }

        return view('orders.showorder', compact('order'));
    }

    public function notice($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        if ($order->user_id && (!Auth::check() || Auth::id() !== (int) $order->user_id)) {
            return redirect('/')->with('error', 'Bạn không có quyền xem đơn này.');
        }

        return view('orders.notice', compact('order'));
    }

    public function applyPromotion($id, Request $request)
    {
        $order = Order::with(['orderItems.product'])->findOrFail($id);

        $data = $request->validate([
            'code'     => 'required|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $code = strtoupper(trim($data['code']));

        $promo = Promotion::where('code', $code)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return back()->with('error', 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn.');
        }

        $subtotalAll = $order->orderItems->sum(fn ($i) => (int) $i->price * (int) $i->quantity);
        $shippingFee = (int) ($order->shipping_fee ?? 0);

        if ($promo->min_order_value && $subtotalAll < (int) $promo->min_order_value) {
            return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã.');
        }

        $discount = 0;
        if ($promo->discount_type === 'percent') {
            $discount = (int) floor($subtotalAll * ((float) $promo->discount_value / 100));
            if (!empty($promo->max_discount_value)) {
                $discount = min($discount, (int) $promo->max_discount_value);
            }
        } else {
            $discount = (int) $promo->discount_value;
        }

        $discount = max(0, min($discount, $subtotalAll));

        $order->discount = $discount;
        $order->promotion_code = $promo->code;
        $order->total = max(0, $subtotalAll - $discount + $shippingFee);
        $order->save();

        return back()->with('success', 'Áp dụng mã khuyến mãi thành công.');
    }

    public function removePromotion($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        $subtotalAll = $order->orderItems->sum(fn ($i) => (int) $i->price * (int) $i->quantity);
        $shippingFee = (int) ($order->shipping_fee ?? 0);

        $order->discount = 0;
        $order->promotion_code = null;
        $order->total = $subtotalAll + $shippingFee;
        $order->save();

        return back()->with('success', 'Đã gỡ mã khuyến mãi.');
    }

    public function updateQuantity(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        if ($order->user_id && (!Auth::check() || Auth::id() !== (int) $order->user_id)) {
            return back()->with('error', 'Bạn không có quyền thao tác đơn này.');
        }

        if (!in_array($order->status, ['pending', 'awaiting_payment'], true)) {
            return back()->with('error', 'Chỉ được sửa số lượng khi đơn chưa xử lý.');
        }

        $data = $request->validate([
            'item_id'   => ['required', 'integer'],
            'quantity'  => ['required', 'integer', 'min:1'],
        ]);

        $item = $order->orderItems->firstWhere('id', (int) $data['item_id']);
        if (!$item) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong đơn.');
        }

        $newQty = (int) $data['quantity'];
        $oldQty = (int) $item->quantity;

        if ($newQty === $oldQty) {
            return back()->with('success', 'Không có thay đổi.');
        }

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            $diff = $newQty - $oldQty;

            if ($diff > 0) {
                if ((int) $product->quantity < $diff) {
                    throw new \Exception('Số lượng hàng còn không đủ.');
                }
                $product->quantity = (int) $product->quantity - $diff;
            } else {
                $product->quantity = (int) $product->quantity + abs($diff);
            }

            $product->save();

            $item->quantity = $newQty;
            $item->save();

            $subtotalAll = $order->orderItems()->get()->sum(fn ($i) => (int) $i->price * (int) $i->quantity);
            $shippingFee = (int) ($order->shipping_fee ?? 0);
            $discount = (int) ($order->discount ?? 0);

            $order->total = max(0, $subtotalAll - $discount + $shippingFee);
            $order->save();

            DB::commit();

            return back()->with('success', 'Đã cập nhật số lượng sản phẩm trong đơn.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Không thể cập nhật số lượng.');
        }
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        if ($order->user_id && (!Auth::check() || Auth::id() !== (int) $order->user_id)) {
            return back()->with('error', 'Bạn không có quyền hủy đơn này.');
        }

        if (!in_array($order->status, ['pending', 'awaiting_payment'], true)) {
            return back()->with('error', 'Đơn này không thể hủy.');
        }

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            foreach ($order->orderItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product && (int) $order->stock_deducted === 1) {
                    $product->quantity = (int) $product->quantity + (int) $item->quantity;
                    $product->save();
                }
            }

            $order->status = 'cancelled';
            $order->refund_note = 'Khách hàng hủy đơn: ' . $data['cancel_reason'];
            $order->save();

            DB::commit();

            return back()->with('success', 'Đã hủy đơn hàng.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Không thể hủy đơn.');
        }
    }

    private function toIntPrice($value): int
    {
        return (int) preg_replace('/\D+/', '', (string) $value);
    }

    private function finalPrice(Product $product): int
    {
        $price = (int) ($product->price ?? 0);
        $isHotdeal = (bool) ($product->is_hotdeal ?? false);
        $discountPercent = (int) ($product->discount_percent ?? 0);

        if ($isHotdeal && $discountPercent > 0) {
            return (int) floor($price * (100 - $discountPercent) / 100);
        }

        return $price;
    }
}
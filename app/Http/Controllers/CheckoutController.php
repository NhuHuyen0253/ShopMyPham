<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session()->get('cart', []);
        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,vnpay',
            'shipping_fee' => 'required|numeric|min:0'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')
                ->with('error', 'Giỏ hàng của bạn hiện tại trống!');
        }

        // ✅ tính tiền hàng
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $shippingFee = (int) $request->shipping_fee;

        // 👉 model bạn dùng field total
        $total = $subtotal + $shippingFee;

        // ===== LƯU ORDER =====
        $order = \App\Models\Order::create([
            'user_id' => Auth::id(),
            'status' => 'pending',

            'receiver_name' => $request->fullname,
            'receiver_phone' => $request->phone,
            'receiver_addr' => $request->address,

            'payment_method' => $request->payment_method,
            'is_paid' => $request->payment_method === 'vnpay' ? 1 : 0,

            'shipping_carrier' => 'GHN',
            'shipping_fee' => $shippingFee,

            'total' => $total
        ]);

        // ===== LƯU ORDER ITEMS =====
        foreach ($cart as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'price' => $item['price'],
                'quantity' => $item['qty'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('order.success', $order->id)
            ->with('success', 'Đặt hàng thành công!');
    }
}

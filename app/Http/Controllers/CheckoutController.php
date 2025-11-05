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
        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Giỏ hàng của bạn hiện tại trống!');
        }

        // Xử lý thanh toán (ví dụ tích hợp thanh toán với Stripe hoặc PayPal)
        // Sau khi thanh toán thành công:
        // - Xóa giỏ hàng trong session
        // - Lưu thông tin đơn hàng vào database

        session()->forget('cart');
        return redirect()->route('cart.view')->with('success', 'Thanh toán thành công, đơn hàng của bạn đang được xử lý!');
    }
}

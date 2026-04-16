<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // hoặc route('home')
        }

        return back()->withErrors([
            'phone' => 'Số điện thoại hoặc mật khẩu không đúng.',
        ]);
    }

    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        // 1. Backup giỏ hàng (và các thông tin liên quan nếu cần)
        $cart        = $request->session()->get('cart', []);
        $cartTotals  = $request->session()->get('cart_totals');        // nếu bạn có dùng
        $promoCode   = $request->session()->get('cart_promo_code');    // nếu có
        $cartDiscount = $request->session()->get('cart_discount');     // nếu có

        // 2. Logout user như bình thường
        Auth::logout();

        // 3. Invalidate session cũ (bảo mật)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4. Restore giỏ hàng vào session mới
        if (!empty($cart)) {
            $request->session()->put('cart', $cart);
        }
        if (!empty($cartTotals)) {
            $request->session()->put('cart_totals', $cartTotals);
        }
        if (!empty($promoCode)) {
            $request->session()->put('cart_promo_code', $promoCode);
        }
        if (!empty($cartDiscount)) {
            $request->session()->put('cart_discount', $cartDiscount);
        }

        // 5. Điều hướng về trang chủ / trang bạn muốn
        return redirect()->route('home'); // hoặc route khác bạn dùng
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function logout(Request $request)
    {
        $cart = session('cart', []);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->put('cart', $cart);

        return redirect('/'); // Trở về trang chủ
    }
    public function info()
{
    return view('info'); 

}

}
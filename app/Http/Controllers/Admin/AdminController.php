<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function dashboard()
    {
       $totalProducts = Product::count();
        $todayOrders = Order::whereDate('created_at', now()->toDateString())->count();
        $totalUsers = User::count();
        $newProducts = Product::latest()->take(1)->get();
        $newOrders = Order::latest()->take(1)->with('user')->get();
        $lowStock = Product::where('quantity', '<', 5)->get();

        return view('admin.dashboard', compact('totalProducts', 'todayOrders', 'totalUsers','newProducts', 'newOrders', 'lowStock'));
    }
    // Hiển thị form thay đổi mật khẩu
    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }

    // Xử lý cập nhật mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::guard('admin')->user(); // Nếu không có guard riêng, dùng Auth::user()

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không chính xác.');
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }


}


    
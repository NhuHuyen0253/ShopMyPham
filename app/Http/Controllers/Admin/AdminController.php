<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalProducts = Product::count();
        $todayOrders = Order::whereDate('created_at', now()->toDateString())->count();
        $totalUsers = User::count();

        // 1. Thông báo đơn hàng mới: chỉ lấy đơn chưa xử lý
        $newOrders = Order::with('user')
            ->whereIn('status', ['pending', 'awaiting_payment'])
            ->latest()
            ->simplePaginate(5, ['*'], 'orders_page');

        // 2. Thông báo sản phẩm mới: chỉ lấy sản phẩm tạo trong 3 ngày gần đây
        $newProducts = Product::where('created_at', '>=', Carbon::now()->subDays(3))
            ->latest()
            ->simplePaginate(5, ['*'], 'products_page');

        // 3. Thông báo sắp hết hàng: chỉ hiện khi số lượng còn ít
        $lowStock = Product::where('quantity', '<', 5)
            ->latest()
            ->simplePaginate(5, ['*'], 'lowstock_page');

        return view('admin.dashboard', compact(
            'totalProducts',
            'todayOrders',
            'totalUsers',
            'newProducts',
            'newOrders',
            'lowStock'
        ));
    }

    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không chính xác.');
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return redirect()->route('admin.dashboard')->with('success', 'Đổi mật khẩu thành công ✅');
    }
}
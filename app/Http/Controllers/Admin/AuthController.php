<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin; 
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class AuthController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // Phương thức để hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('admin.auth.login'); // Trả về view đăng nhập của admin
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

         if (Auth::guard('admin')->attempt($credentials)) {
        return redirect()->intended('/admin/dashboard');  // Chuyển hướng đến dashboard nếu đăng nhập thành công
    }

        return back()->withErrors(['phone' => 'Thông tin đăng nhập không đúng.']);
    }

    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('admin.auth.register'); // Đảm bảo bạn có view này
    }

    // Xử lý đăng ký admin
    public function register(Request $request)
    {
        // Xác thực dữ liệu đăng ký
        $validated = $request->validate([
            'phone' => 'required|unique:admins,phone',
            'name' => 'required|string|max:255',
            'password' => 'required|confirmed|min:8',
        ], [
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký.',
            'name.required' => 'Tên người dùng là bắt buộc.',
            'name.string' => 'Tên người dùng phải là một chuỗi ký tự.',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        // Tạo admin mới
        $admin = Admin::create([
            'phone' => $request->phone,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // Đăng nhập admin mới sau khi đăng ký
        Auth::guard('admin')->login($admin);

        // Chuyển hướng đến trang dashboard admin
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Đăng xuất người dùng
        $request->session()->invalidate(); // Hủy bỏ phiên làm việc
        $request->session()->regenerateToken(); // Tạo lại token bảo mật

        return redirect()->route('admin.login.form'); // Chuyển hướng về trang đăng nhập
    }

}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show()
    {
        return view('account-info');
    }

    public function update(Request $request)
{
    $user = Auth::user();

    // Kiểm tra nếu giá trị phone không phải là null
    if (empty($request->phone)) {
        return redirect()->back()->with('error', 'Số điện thoại không thể trống.');
    }
     $request->validate([
        'phone' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'gender' => 'required|string',
        'dob' => 'required|date',
    ]);

    // Cập nhật các trường
    $user->name = $request->name;
    $user->gender = $request->gender;
    $user->phone = $request->phone; // Kiểm tra rằng phone không phải null
    $user->dob = $request->year . '-' . $request->month . '-' . $request->day;
    $user->save();

    return redirect()->route('account.info')->with('success', 'Thông tin tài khoản đã được cập nhật!');
}

}

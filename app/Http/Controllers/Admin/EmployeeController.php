<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên
     */
    // App/Http/Controllers/Admin/EmployeeController.php
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $employeesQuery = Employee::query();

        if ($q !== '') {
            $employeesQuery->where(function($qr) use ($q) {
                $qr->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('position', 'like', "%{$q}%");
            });
        }

        // nếu muốn vừa lọc vừa phân trang:
        $employees = $employeesQuery->latest('id')->paginate(10)->appends(['q' => $q]);

        // các ID sẽ được tô màu (ở đây chính là toàn bộ kết quả sau lọc)
        $highlightIds = $q !== '' ? $employees->pluck('id')->all() : [];

        return view('admin.employee.index', compact('employees', 'q', 'highlightIds'));
    }

    /**
     * Hiển thị form thêm nhân viên mới
     */
    public function create()
    {
        return view('admin.employee.create');
    }

    /**
     * Lưu thông tin nhân viên mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Xử lý ảnh đại diện nếu có
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Lưu thông tin nhân viên
        Employee::create([
            'name' => $request->input('name'),
            'dob' => $request->input('dob'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'position' => $request->input('position'),
            'hire_date' => $request->input('hire_date'),
            'status' => $request->input('status'),
            'avatar' => $avatarPath ?? null,
        ]);

        // Redirect về trang danh sách nhân viên với thông báo thành công
        return redirect()->route('admin.employee.index')->with('success', 'Nhân viên đã được thêm!');
    }

    /**
     * Hiển thị form chỉnh sửa thông tin nhân viên
     */
    public function edit($id)
    {
        // Tìm nhân viên theo ID
        $employee = Employee::findOrFail($id);

        // Trả về view chỉnh sửa nhân viên
        return view('admin.employee.edit', compact('employee'));
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email,'. $id .',id',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tìm nhân viên theo ID
        $employee = Employee::findOrFail($id);

        // Xử lý ảnh đại diện nếu có
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($employee->avatar) {
                Storage::delete('public/' . $employee->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật thông tin nhân viên
        $employee->update([
            'name' => $request->input('name'),
            'dob' => $request->input('dob'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'position' => $request->input('position'),
            'hire_date' => $request->input('hire_date'),
            'status' => $request->input('status'),
            'avatar' => $avatarPath ?? $employee->avatar,  // Nếu không có ảnh mới, giữ ảnh cũ
        ]);

        // Redirect về trang danh sách nhân viên với thông báo thành công
        return redirect()->route('admin.employee.index')->with('success', 'Nhân viên đã được cập nhật!');
    }

    /**
     * Xóa nhân viên
     */
    public function destroy($id)
    {
        // Tìm nhân viên theo ID
        $employee = Employee::findOrFail($id);

        // Xóa ảnh đại diện nếu có
        if ($employee->avatar) {
           Storage::disk('public')->delete($employee->avatar);
        }

        // Xóa nhân viên khỏi cơ sở dữ liệu
        $employee->delete();

        // Redirect về trang danh sách nhân viên với thông báo thành công
        return redirect()->route('admin.employee.index')->with('success', 'Nhân viên đã được xóa!');
    }
}

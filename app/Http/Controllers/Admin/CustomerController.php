<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /admin/customers
    public function index(Request $request)
{
    $q      = $request->input('q');                 // string|null
    $status = $request->input('status');            // 'active' | 'blocked' | null
    $sort   = $request->input('sort', 'latest');    // 'latest' | 'name' | 'orders' | 'spent'

    $customers = User::query()
        ->when(function ($qB) {
            if (schemaHasColumn('users', 'is_admin')) $qB->where('is_admin', false);
        })
        ->when($q, function ($qB) use ($q) {
            $qB->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        })
        ->when($status === 'active', fn($qB) => $qB->where('is_blocked', false))
        ->when($status === 'blocked', fn($qB) => $qB->where('is_blocked', true))
        ->withCount('orders')
        ->withSum('orders as total_spent', 'total')
        ->when($sort === 'name', function ($qB) {
            // Sắp xếp theo "tên" (từ cuối), có xử lý khoảng trắng & NULL
            $qB->orderByRaw("
                LOWER(
                    TRIM(
                        SUBSTRING_INDEX(
                            TRIM(COALESCE(name, '')),
                            ' ',
                            -1
                        )
                    )
                ) ASC
            ")->orderBy('name'); // tie-break nếu trùng tên
        })

        ->when($sort === 'orders', fn($qB) => $qB->orderByDesc('orders_count'))
        ->when($sort === 'spent', fn($qB) => $qB->orderByDesc('total_spent'))
        ->when($sort === 'latest' || empty($sort), fn($qB) => $qB->latest())
        ->paginate(12);

    return view('admin.customers.index', compact('customers'));
}

    // GET /admin/customers/{user}
    public function show(User $user)
    {
        $user->loadCount('orders')
            ->loadSum('orders as total_spent', 'total');

        $orders = $user->orders()
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', [
            'customer' => $user,
            'orders' => $orders,
        ]);
    }

    // PATCH /admin/customers/{user}/toggle
    public function toggleStatus(User $user)
    {
        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        return back()->with('success', $user->is_blocked ? 'Đã chặn khách hàng.' : 'Đã mở khóa khách hàng.');
    }

    // DELETE /admin/customers/{user}
    public function destroy(User $user)
    {
        // Nếu muốn xóa mềm, đảm bảo User có SoftDeletes, hoặc thay bằng "deactivate".
        $user->delete();

        return back()->with('success', 'Đã xóa khách hàng.');
    }
}

/**
 * Helper để check cột có tồn tại (tránh lỗi nếu không dùng is_admin).
 */
if (! function_exists('schemaHasColumn')) {
    function schemaHasColumn($table, $column) {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

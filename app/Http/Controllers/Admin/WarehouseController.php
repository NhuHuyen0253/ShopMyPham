<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // GET /admin/warehouses (name: admin.warehouses.index)
    public function index(Request $request)
    {
        $q = $request->input('q');

        $warehouses = Warehouse::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('location', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.warehouses.index', compact('warehouses', 'q'));
    }

    // GET /admin/warehouses/create (name: admin.warehouses.create)
    public function create()
    {
        return view('admin.warehouses.create');
    }

    // POST /admin/warehouses (name: admin.warehouses.store)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:warehouses,name',
            'location' => 'nullable|string|max:255',
        ]);

        Warehouse::create($data);

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Đã tạo kho mới.');
    }

    // GET /admin/warehouses/{warehouse}/edit (name: admin.warehouses.edit)
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    // PUT/PATCH /admin/warehouses/{warehouse} (name: admin.warehouses.update)
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:warehouses,name,' . $warehouse->id,
            'location' => 'nullable|string|max:255',
        ]);

        $warehouse->update($data);

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Đã cập nhật kho.');
    }

    // DELETE /admin/warehouses/{warehouse} (name: admin.warehouses.destroy)
    public function destroy(Warehouse $warehouse)
    {
        // Nếu có ràng buộc (stock_levels, stock_movements), hãy kiểm tra trước khi xoá.
        $warehouse->delete();

        return back()->with('success', 'Đã xoá kho.');
    }
}

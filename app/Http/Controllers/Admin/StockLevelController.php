<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockLevelController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $stockLevels = StockLevel::with(['product', 'warehouse'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->whereHas('product', function ($productQuery) use ($q) {
                        $productQuery->where('name', 'like', "%{$q}%");
                    })->orWhereHas('warehouse', function ($warehouseQuery) use ($q) {
                        $warehouseQuery->where('name', 'like', "%{$q}%")
                                       ->orWhere('location', 'like', "%{$q}%");
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.stock_levels.index', compact('stockLevels', 'q'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.stock_levels.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'warehouse_id'  => ['required', 'exists:warehouses,id'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
        ], [
            'product_id.required'   => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'     => 'Sản phẩm không tồn tại.',
            'warehouse_id.required' => 'Vui lòng chọn kho.',
            'warehouse_id.exists'   => 'Kho không tồn tại.',
            'quantity.required'     => 'Vui lòng nhập số lượng tồn.',
            'quantity.integer'      => 'Số lượng tồn phải là số nguyên.',
            'quantity.min'          => 'Số lượng tồn không được nhỏ hơn 0.',
            'reorder_point.integer' => 'Mức cảnh báo phải là số nguyên.',
            'reorder_point.min'     => 'Mức cảnh báo không được nhỏ hơn 0.',
        ]);

        $exists = StockLevel::where('product_id', $data['product_id'])
            ->where('warehouse_id', $data['warehouse_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'product_id' => 'Sản phẩm này đã có tồn kho trong kho đã chọn.',
                ])
                ->withInput();
        }

        StockLevel::create([
            'product_id'    => $data['product_id'],
            'warehouse_id'  => $data['warehouse_id'],
            'quantity'      => (int) $data['quantity'],
            'reorder_point' => (int) ($data['reorder_point'] ?? 0),
        ]);

        return redirect()
            ->route('admin.stock_levels.index')
            ->with('success', 'Thêm tồn kho thành công.');
    }

    public function edit(StockLevel $stockLevel)
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.stock_levels.edit', compact('stockLevel', 'products', 'warehouses'));
    }

    public function update(Request $request, StockLevel $stockLevel)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'warehouse_id'  => ['required', 'exists:warehouses,id'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
        ], [
            'product_id.required'   => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'     => 'Sản phẩm không tồn tại.',
            'warehouse_id.required' => 'Vui lòng chọn kho.',
            'warehouse_id.exists'   => 'Kho không tồn tại.',
            'quantity.required'     => 'Vui lòng nhập số lượng tồn.',
            'quantity.integer'      => 'Số lượng tồn phải là số nguyên.',
            'quantity.min'          => 'Số lượng tồn không được nhỏ hơn 0.',
            'reorder_point.integer' => 'Mức cảnh báo phải là số nguyên.',
            'reorder_point.min'     => 'Mức cảnh báo không được nhỏ hơn 0.',
        ]);

        $exists = StockLevel::where('product_id', $data['product_id'])
            ->where('warehouse_id', $data['warehouse_id'])
            ->where('id', '!=', $stockLevel->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'product_id' => 'Sản phẩm này đã có tồn kho trong kho đã chọn.',
                ])
                ->withInput();
        }

        $stockLevel->update([
            'product_id'    => $data['product_id'],
            'warehouse_id'  => $data['warehouse_id'],
            'quantity'      => (int) $data['quantity'],
            'reorder_point' => (int) ($data['reorder_point'] ?? 0),
        ]);

        return redirect()
            ->route('admin.stock_levels.index')
            ->with('success', 'Cập nhật tồn kho thành công.');
    }

    public function destroy(StockLevel $stockLevel)
    {
        $stockLevel->delete();

        return redirect()
            ->route('admin.stock_levels.index')
            ->with('success', 'Xóa tồn kho thành công.');
    }
}
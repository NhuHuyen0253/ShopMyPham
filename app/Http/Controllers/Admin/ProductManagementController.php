<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use Illuminate\Http\Request;

class ProductManagementController extends Controller
{
    // Danh sách tồn theo sản phẩm
    public function index(Request $request)
    {
        $q = trim((string)$request->input('q', ''));

        $products = Product::query()
            ->select(['id','name','image']) // đổi 'image' theo cột ảnh của bạn
            ->selectSub(function($sub){
                $sub->from('stock_levels')
                    ->selectRaw('COALESCE(SUM(quantity),0)')
                    ->whereColumn('stock_levels.product_id','products.id');
            }, 'total_stock')
            ->with(['stockLevels.warehouse:id,name'])          // để hiện theo từng kho
            ->when($q !== '', fn($qry) => $qry->where('name','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.product_management.index', compact('products','q'));
    }

    // Chỉnh sửa: xem tồn theo kho + cập nhật reorder_point
    public function edit(Product $product)
    {
        $product->load(['stockLevels.warehouse:id,name']);
        return view('admin.product_management.edit', compact('product'));
    }

    // Lưu ngưỡng cảnh báo cho nhiều kho một lúc
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'reorder_point' => 'nullable|array',
            'reorder_point.*' => 'nullable|integer|min:0',
        ]);
        $rows = $data['reorder_point'] ?? [];
        foreach ($data['reorder_point'] as $warehouseId => $rp) {
            /** @var StockLevel $level */
            $level = StockLevel::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => (int)$warehouseId],
                ['quantity' => 0, 'reorder_point' => 0]
            );
            $level->update(['reorder_point' => (int)($rp ?? 0)]);
        }

        return redirect()->route('admin.product_management.index')->with('success','Đã cập nhật ngưỡng cảnh báo.');
    }
}

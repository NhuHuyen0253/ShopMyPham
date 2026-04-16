<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        // Danh sách sản phẩm để hiển thị bảng (có phân trang)
        $products = Product::with(['supplier', 'defaultWarehouse'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        // Toàn bộ sản phẩm để đổ vào select nhập kho
        $allProducts = Product::orderBy('name')->get(['id', 'name', 'quantity','default_warehouse_id']);

        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $recentMovements = StockMovement::with(['product', 'warehouse', 'supplier'])
            ->orderByDesc('moved_at')
            ->orderByDesc('id')
            ->take(20)
            ->get();

        $lowStockProducts = Product::where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->orderBy('name')
            ->get(['id', 'name', 'quantity']);

        return view('admin.stock.index', compact(
            'products',
            'allProducts',
            'suppliers',
            'warehouses',
            'recentMovements',
            'q',
            'lowStockProducts'
        ));
    }

    public function stockIn(Request $request)
    {
        $data = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'warehouse_id'   => ['required', 'exists:warehouses,id'],
            'supplier_id'    => ['nullable', 'exists:suppliers,id'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'unit_cost'      => ['nullable', 'numeric', 'min:0'],
            'reference_code' => ['nullable', 'string', 'max:255'],
            'note'           => ['nullable', 'string'],
        ], [
            'product_id.required'   => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'     => 'Sản phẩm không tồn tại.',
            'warehouse_id.required' => 'Vui lòng chọn kho.',
            'warehouse_id.exists'   => 'Kho không tồn tại.',
            'supplier_id.exists'    => 'Nhà cung cấp không tồn tại.',
            'quantity.required'     => 'Vui lòng nhập số lượng.',
            'quantity.integer'      => 'Số lượng phải là số nguyên.',
            'quantity.min'          => 'Số lượng phải lớn hơn 0.',
            'unit_cost.numeric'     => 'Giá nhập phải là số.',
            'unit_cost.min'         => 'Giá nhập không được âm.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $product = Product::lockForUpdate()->findOrFail($data['product_id']);

                $qty = (int) $data['quantity'];
                $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                    ? (float) $data['unit_cost']
                    : (float) ($product->original_price ?? 0);

                // Cập nhật số lượng tồn
                $product->quantity = (int) ($product->quantity ?? 0) + $qty;

                // Cập nhật giá nhập gần nhất nếu có nhập giá
                if (array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null) {
                    $product->original_price = $unitCost;
                }

                // Cập nhật supplier mặc định nếu chưa có hoặc nếu admin chọn supplier mới
                if (!empty($data['supplier_id'])) {
                    $product->supplier_id = $data['supplier_id'];
                }

                // Cập nhật kho mặc định theo kho nhập
                $product->default_warehouse_id = $data['warehouse_id'];

                $product->save();

                StockMovement::create([
                    'product_id'     => $product->id,
                    'warehouse_id'   => $data['warehouse_id'],
                    'type'           => 'in',
                    'quantity'       => $qty,
                    'unit_cost'      => $unitCost,
                    'supplier_id'    => $data['supplier_id'] ?? $product->supplier_id,
                    'reference_code' => $data['reference_code'] ?? null,
                    'note'           => $data['note'] ?? 'Nhập kho thủ công từ admin',
                    'moved_at'       => now(),
                ]);
            });

            return redirect()
                ->route('admin.stock.index')
                ->with('success', 'Nhập kho thành công.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Lỗi nhập kho: ' . $e->getMessage());
        }
    }
    public function stockOut(Request $request)
    {
        $data = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'warehouse_id'   => ['required', 'exists:warehouses,id'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'reference_code' => ['nullable', 'string', 'max:255'],
            'note'           => ['nullable', 'string'],
        ], [
            'product_id.required'   => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'     => 'Sản phẩm không tồn tại.',
            'warehouse_id.required' => 'Vui lòng chọn kho.',
            'warehouse_id.exists'   => 'Kho không tồn tại.',
            'quantity.required'     => 'Vui lòng nhập số lượng xuất.',
            'quantity.integer'      => 'Số lượng xuất phải là số nguyên.',
            'quantity.min'          => 'Số lượng xuất phải lớn hơn 0.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $product = Product::lockForUpdate()->findOrFail($data['product_id']);

                $qty = (int) $data['quantity'];
                $currentQty = (int) ($product->quantity ?? 0);

                if ($currentQty < $qty) {
                    throw new \Exception('Số lượng tồn không đủ để xuất kho. Hiện chỉ còn ' . $currentQty . ' sản phẩm.');
                }

                $product->quantity = $currentQty - $qty;

                // Có thể cập nhật kho mặc định theo kho xuất nếu muốn
                $product->default_warehouse_id = $data['warehouse_id'];

                $product->save();

                StockMovement::create([
                    'product_id'     => $product->id,
                    'warehouse_id'   => $data['warehouse_id'],
                    'type'           => 'out',
                    'quantity'       => $qty,
                    'unit_cost'      => (float) ($product->original_price ?? 0),
                    'supplier_id'    => $product->supplier_id,
                    'reference_code' => $data['reference_code'] ?? null,
                    'note'           => $data['note'] ?? 'Xuất kho thủ công - bán trực tiếp tại cửa hàng',
                    'moved_at'       => now(),
                ]);
            });

            return redirect()
                ->route('admin.stock.index')
                ->with('success', 'Xuất kho thành công.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Lỗi xuất kho: ' . $e->getMessage());
        }
    }

    public function updateSku(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
        ], [
            'sku.required' => 'Vui lòng nhập SKU.',
            'sku.max'      => 'SKU không được quá 100 ký tự.',
            'sku.unique'   => 'SKU đã tồn tại.',
        ]);

        $product->sku = trim($data['sku']);
        $product->save();

        return back()->with('success', 'Đã cập nhật SKU.');
    }

    public function history(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $type = $request->get('type', 'in'); // mặc định xem nhập kho
        $perPage = 15;

        $movements = StockMovement::with(['product', 'warehouse', 'supplier'])
            ->when($type !== '', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('reference_code', 'like', "%{$q}%")
                        ->orWhere('note', 'like', "%{$q}%")
                        ->orWhereHas('product', function ($p) use ($q) {
                            $p->where('name', 'like', "%{$q}%")
                              ->orWhere('sku', 'like', "%{$q}%");
                        })
                        ->orWhereHas('supplier', function ($s) use ($q) {
                            $s->where('name', 'like', "%{$q}%");
                        })
                        ->orWhereHas('warehouse', function ($w) use ($q) {
                            $w->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('moved_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('admin.stock.history', compact('movements', 'q', 'type'));
    }
}
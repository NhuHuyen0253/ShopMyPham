<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('products')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'discount_percent' => 'required|integer|min:1|max:100',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'is_active'        => 'nullable',
            'product_ids'      => 'nullable|array',
            'product_ids.*'    => 'exists:products,id',
        ]);

        $promotion = Promotion::create([
            'name'             => $data['name'],
            'description'      => $data['description'] ?? null,
            'discount_percent' => $data['discount_percent'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'is_active'        => $request->has('is_active'),
        ]);

        $promotion->products()->sync($data['product_ids'] ?? []);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Tạo chương trình khuyến mãi thành công!');
    }

    public function edit(Promotion $promotion)
    {
        $products = Product::orderBy('name')->get();
        $selectedProducts = $promotion->products()->pluck('products.id')->toArray();

        return view('admin.promotions.edit', compact('promotion', 'products', 'selectedProducts'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'discount_percent' => 'required|integer|min:1|max:100',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'is_active'        => 'nullable',
            'product_ids'      => 'nullable|array',
            'product_ids.*'    => 'exists:products,id',
        ]);

        $promotion->update([
            'name'             => $data['name'],
            'description'      => $data['description'] ?? null,
            'discount_percent' => $data['discount_percent'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'is_active'        => $request->has('is_active'),
        ]);

        $promotion->products()->sync($data['product_ids'] ?? []);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Cập nhật chương trình khuyến mãi thành công!');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->products()->detach();
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Xóa chương trình khuyến mãi thành công!');
    }
}
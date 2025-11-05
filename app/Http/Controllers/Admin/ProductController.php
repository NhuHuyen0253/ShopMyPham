<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brands::all();
        return view('admin.product.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'quantity', 'category_id', 'brand_id']);

        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $request->image->move(public_path('images/product'), $imageName);
            $data['image'] = $imageName;
        }

        Product::create($data);

        return redirect()->route('admin.product.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brands::all();
        return view('admin.product.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'quantity', 'category_id', 'brand_id']);

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu có
            if ($product->image && file_exists(public_path('images/product/' . $product->image))) {
                unlink(public_path('images/product/' . $product->image));
            }

            $file      = $request->file('image');
            $imageName = Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/product'), $imageName);
            $data['image'] = $imageName;
        }
        $product->update($data);

        return redirect()->route('admin.product.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(Product $product)
    {
        // Xoá ảnh nếu có
        $imagePath = public_path('images/product/' . $product->image);
        if ($product->image && File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Đã xoá sản phẩm.');
    }
}

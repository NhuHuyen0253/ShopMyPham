<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();

        $brands = Brands::query()
            ->when($q->isNotEmpty(), fn($query) =>
                $query->where('name', 'like', '%'.$q.'%')
            )
            ->orderByDesc('id')
            ->paginate(10) // tuỳ chỉnh số trang
            ->appends($request->only('q'));

        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('images/brand'), $filename);
            $data['image'] = $filename;
        }

        Brands::create($data);

        return redirect()->route('admin.brand.index')->with('success', 'Thương hiệu đã được thêm.');
    }


    public function edit(Brands $brand)
    {
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, Brands $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('images/brand'), $filename);
            $data['image'] = $filename;
        }

        $brand->update($data);

        return redirect()->route('admin.brand.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(Brands $brand)
    {
        if ($brand->image && \Storage::disk('public')->exists($brand->image)) {
            \Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();
        return back()->with('success', 'Đã xoá thương hiệu.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $type = $request->type;

        $categories = Category::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('slug', 'like', '%' . $keyword . '%');
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'nullable|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.category.index')
            ->with('success', 'Thêm danh mục thành công');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'type' => 'nullable|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;

        while (
            Category::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.category.index')
            ->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return redirect()->route('admin.category.index')
                ->with('error', 'Danh mục đang có sản phẩm, không thể xóa');
        }

        $category->delete();

        return redirect()->route('admin.category.index')
            ->with('success', 'Xóa danh mục thành công');
    }
}
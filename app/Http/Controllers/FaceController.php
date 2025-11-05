<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class FaceController extends Controller
{
    public function index()
{
    $categories = Category::where('type', 'face')->get();

    // Cách 1: dùng whereHas (cần quan hệ category())
    $products = Product::whereHas('category', function ($q) {
        $q->where('type', 'face');
    })->paginate(9);

    return view('face', compact('categories', 'products'));
}

public function category($slug)
{
    $categories = Category::where('type', 'face')->get();

    $category = Category::where('type', 'face')
        ->where('slug', $slug)
        ->firstOrFail();

    $products = Product::where('category_id', $category->id)->paginate(9);
    // hoặc: $products = $category->products()->paginate(9);

    return view('face', compact('categories', 'products'));
}

}

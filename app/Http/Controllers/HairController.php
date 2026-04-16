<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HairController extends Controller
{
    public function index()
    {
        $categories = Category::where('type', 'hair')->get();
        $products = Product::whereHas('category', function ($q) {
            $q->where('type', 'hair');
        })->paginate(9);
        
        return view('hair', compact('categories', 'products'));
    }

    public function category($slug)
{
    $categories = Category::where('type', 'hair')->get();

    $category = Category::where('type', 'hair')
        ->where('slug', $slug)
        ->firstOrFail();

    $products = Product::where('category_id', $category->id)->paginate(9);
    // hoặc: $products = $category->products()->paginate(9);

    return view('hair', compact('categories', 'products'));
}
}

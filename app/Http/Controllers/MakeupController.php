<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class MakeupController extends Controller
{
    public function index()
    {
        $categories = Category::where('type', 'Makeup')->get();
        $products = Product::whereHas('category', function ($q) {
            $q->where('type', 'Makeup');
        })->paginate(9);
        
        return view('Makeup', compact('categories', 'products'));
    }

    public function category($slug)
    {
        $categories = Category::where('type', 'Makeup')->get();

        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->paginate(9);

        // Gửi thêm $category sang view
        return view('Makeup', compact('categories', 'products', 'category'));
    }

}

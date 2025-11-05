<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class BodyController extends Controller
{
    public function index()
    {
        $categories = Category::where('type', 'Body')->get();
        $products = Product::whereHas('category', function ($q) {
            $q->where('type', 'Body');
        })->paginate(9);
        
        return view('Body', compact('categories', 'products'));
    }

    public function category($slug)
    {
        $categories = Category::where('type', 'Body')->get();

        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->paginate(9);

        return view('Body', compact('categories', 'products'));
    }
}

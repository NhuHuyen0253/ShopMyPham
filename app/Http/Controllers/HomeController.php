<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với danh sách sản phẩm.
     */
    public function index()
    {
        // Lấy toàn bộ sản phẩm từ CSDL
        $products = Product::paginate(12);

        // Trả về view home.blade.php kèm danh sách sản phẩm
         return view('home', compact('products')); 
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PromoBanner;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với danh sách sản phẩm + nhiều banner khuyến mãi.
     */
    public function index(Request $request)
    {
        // Sản phẩm mới nhất
        $products = Product::orderByDesc('created_at')
            ->take(12)
            ->get();

        // Lấy nhiều banner đang active, trong thời gian chạy
        $banners = PromoBanner::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('home', compact('products', 'banners'));
    }
}
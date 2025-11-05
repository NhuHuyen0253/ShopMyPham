<?php

namespace App\Http\Controllers;

use App\Models\Brands;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brands::all();

        // Group theo chữ cái đầu
        $grouped = $brands->groupBy(function ($item) {
            $first = strtoupper(substr($item->name, 0, 1));
            return preg_match('/[A-Z]/', $first) ? $first : '0-9';
        })->sortKeys();

        return view('brands', compact('grouped'));
    }

    public function show($slug)
    {
        $brands = Brands::where('slug', $slug)->firstOrFail();

        // Lấy sản phẩm theo quan hệ
        $products = $brands->products()->paginate(12);

        return view('ShowBrand', compact('brands', 'products'));
    }
}

<?php
namespace App\Http\Controllers;
use App\Models\Product;
class HotDealController extends Controller
{
    public function index()
{
    $products = Product::where('is_hotdeal', true)->get();
    return view('hotdeal', compact('products'));
}

}

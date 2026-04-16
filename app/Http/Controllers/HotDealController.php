<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class HotDealController extends Controller
{
    public function index(Request $request)
    {
        $promotionId = $request->get('promotion');
        $promotion = null;

        $query = Product::with([
            'images',
            'promotions',
        ]);

        if ($promotionId) {
            $promotion = Promotion::where('id', $promotionId)
                ->where('is_active', 1)
                ->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString())
                ->first();

            if ($promotion) {
                $query->whereHas('promotions', function ($q) use ($promotionId) {
                    $q->where('promotions.id', $promotionId);
                });

                $query->with([
                    'promotions' => function ($q) use ($promotionId) {
                        $q->where('promotions.id', $promotionId);
                    }
                ]);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->where('is_hotdeal', true);
        }

        $products = $query->latest('id')->paginate(12);

        return view('hotdeal', compact('products', 'promotion'));
    }
}
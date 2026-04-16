<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to ?? now()->endOfMonth()->toDateString();

        $orders = Order::with(['items.product'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('is_refunded', 0)
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');

        $estimatedCost = $orders->sum(function ($order) {
            return $order->items->sum(function ($item) {
                return (($item->product->original_price ?? 0) * $item->quantity);
            });
        });

        $estimatedProfit = $totalRevenue - $estimatedCost;

        $lowStockProducts = Product::where('quantity', '<=', 5)->get();

        return view('admin.reports.revenue', compact(
            'from',
            'to',
            'orders',
            'totalOrders',
            'totalRevenue',
            'estimatedCost',
            'estimatedProfit',
            'lowStockProducts'
        ));
    }
}
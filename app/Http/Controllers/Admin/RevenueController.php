<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to   = $request->get('to');

        $completedQuery = Order::query()
            ->where('status', 'completed')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to));

        $cancelledQuery = Order::query()
            ->where('status', 'cancelled')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to));

        $paidQuery = Order::query()
            ->where('status', 'completed')
            ->where('is_paid', 1)
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to));

        $totalRevenue = (clone $completedQuery)
            ->sum(DB::raw('total - COALESCE(shipping_fee, 0)'));

        $totalOrders = (clone $completedQuery)->count();

        $cancelledCount = (clone $cancelledQuery)->count();

        $paidRevenue = (clone $paidQuery)
            ->sum(DB::raw('total - COALESCE(shipping_fee, 0)'));

        $totalDiscount = 0;

        $todayRevenue = Order::query()
            ->where('status', 'completed')
            ->whereDate('created_at', now()->toDateString())
            ->sum(DB::raw('total - COALESCE(shipping_fee, 0)'));

        $monthRevenue = Order::query()
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum(DB::raw('total - COALESCE(shipping_fee, 0)'));

        $orders = Order::query()
            ->where('status', 'completed')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $dailyRevenue = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(total - COALESCE(shipping_fee, 0)) as revenue')
            ->where('status', 'completed')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();

        $monthlyRevenue = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total - COALESCE(shipping_fee, 0)) as revenue")
            ->where('status', 'completed')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderByRaw("MIN(created_at) asc")
            ->get();

        $topProducts = OrderItem::query()
            ->with('product')
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'completed')
            ->when($from, fn($q) => $q->whereDate('orders.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('orders.created_at', '<=', $to))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.revenue.index', compact(
            'from',
            'to',
            'totalRevenue',
            'totalOrders',
            'cancelledCount',
            'paidRevenue',
            'totalDiscount',
            'todayRevenue',
            'monthRevenue',
            'orders',
            'dailyRevenue',
            'monthlyRevenue',
            'topProducts'
        ));
    }
}
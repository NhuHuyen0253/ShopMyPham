<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Lưu lịch sử xem sản phẩm vào session
    $recentlyViewed = session()->get('recently_viewed_products', []);

    // Xóa nếu sản phẩm hiện tại đã có trong danh sách để đẩy lên đầu
    $recentlyViewed = array_values(array_filter(
        $recentlyViewed,
        fn ($productId) => (int) $productId !== (int) $product->id
    ));

    array_unshift($recentlyViewed, $product->id);

    // Giới hạn 12 sản phẩm đã xem gần nhất
    $recentlyViewed = array_slice($recentlyViewed, 0, 12);

    session()->put('recently_viewed_products', $recentlyViewed);

    // Lấy danh sách sản phẩm đã xem trước đó, bỏ sản phẩm hiện tại
    $recentlyViewedIds = array_values(array_filter(
        $recentlyViewed,
        fn ($productId) => (int) $productId !== (int) $product->id
    ));

    $recentlyViewedProducts = collect();

    if (!empty($recentlyViewedIds)) {
        $recentlyViewedProducts = Product::whereIn('id', $recentlyViewedIds)
            ->get()
            ->sortBy(fn ($item) => array_search($item->id, $recentlyViewedIds))
            ->values();
    }

    // Nếu chưa có lịch sử xem thì fallback sang sản phẩm liên quan
    if ($recentlyViewedProducts->isEmpty()) {
        $recentlyViewedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->orderByRaw('brand_id = ? DESC', [$product->brand_id])
            ->limit(8)
            ->get();
    } else {
        // Nếu có lịch sử xem thì chỉ lấy tối đa 8 sản phẩm để hiển thị
        $recentlyViewedProducts = $recentlyViewedProducts->take(8)->values();
    }

    // ===== THÊM PHẦN DUNG TÍCH Ở ĐÂY =====
    $capacityProducts = collect();

    if (!empty($product->group_code)) {
        $capacityProducts = Product::where('group_code', $product->group_code)
            ->whereNotNull('capacity')
            ->orderByRaw("
                CASE
                    WHEN LOWER(capacity) = '30ml' THEN 1
                    WHEN LOWER(capacity) = '50ml' THEN 2
                    WHEN LOWER(capacity) = '75ml' THEN 3
                    WHEN LOWER(capacity) = '100ml' THEN 4
                    WHEN LOWER(capacity) = '120ml' THEN 5
                    WHEN LOWER(capacity) = '150ml' THEN 6
                    WHEN LOWER(capacity) = '180ml' THEN 7
                    WHEN LOWER(capacity) = '200ml' THEN 8
                    WHEN LOWER(capacity) = '250ml' THEN 9
                    WHEN LOWER(capacity) = '300ml' THEN 10
                    WHEN LOWER(capacity) = '350ml' THEN 11
                    WHEN LOWER(capacity) = '400ml' THEN 12
                    WHEN LOWER(capacity) = '500ml' THEN 13
                    WHEN LOWER(capacity) = '700ml' THEN 14
                    WHEN LOWER(capacity) = '1000ml' THEN 15
                    ELSE 999
                END ASC, id ASC
            ")
            ->get();
    }

    // JSON / AJAX (ví dụ cho buynow.js)
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => (int) $product->price,
            'image' => $product->image
                ? asset('images/product/' . $product->image)
                : null,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // -------- PHẦN REVIEW --------
    $ratingFilter = (int) $request->get('rating');

    $reviewsQuery = Review::where('product_id', $product->id)
        ->where('status', 'approved')
        ->latest();

    if ($ratingFilter >= 1 && $ratingFilter <= 5) {
        $reviewsQuery->where('rating', $ratingFilter);
    }

    $reviews = $reviewsQuery->paginate(5)->withQueryString();

    $totalReviews = Review::where('product_id', $product->id)
        ->where('status', 'approved')
        ->count();

    $avgRating = $totalReviews
        ? round(
            Review::where('product_id', $product->id)
                ->where('status', 'approved')
                ->avg('rating'),
            1
        )
        : 0;

    $ratingCountsRaw = Review::where('product_id', $product->id)
        ->where('status', 'approved')
        ->selectRaw('rating, COUNT(*) as total')
        ->groupBy('rating')
        ->pluck('total', 'rating');

    $ratingCounts = [];
    for ($i = 1; $i <= 5; $i++) {
        $ratingCounts[$i] = $ratingCountsRaw[$i] ?? 0;
    }

    $isFavorited = false;

    if (Auth::check()) {
        $isFavorited = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->exists();
    }

    return view('show', [
        'product'                => $product,
        'recentlyViewedProducts' => $recentlyViewedProducts,
        'capacityProducts'       => $capacityProducts, // thêm dòng này
        'reviews'                => $reviews,
        'avgRating'              => $avgRating,
        'totalReviews'           => $totalReviews,
        'ratingCounts'           => $ratingCounts,
        'ratingFilter'           => $ratingFilter,
        'isFavorited'            => $isFavorited,
    ]);
}

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $products = Product::query()
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->latest('id')
            ->paginate(12)
            ->appends(['q' => $q]);

        return view('products.index', compact('products', 'q'));
    }
}
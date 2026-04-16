<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5',
        ]);

        // đã bắt buộc đăng nhập ở route nên luôn có user
        $user = $request->user();

        Review::create([
            'product_id'     => $product->id,
            'user_id'        => $user->id,
            'customer_name'  => $user->name ?? 'Khách hàng',
            'customer_email' => $user->email ?? null,
            'rating'         => $data['rating'],
            'content'        => $data['content'],
            'status'         => 'pending',   // chờ admin duyệt
        ]);

        return back()->with(
            'success',
            'Cảm ơn bạn đã đánh giá! Đánh giá sẽ hiển thị sau khi được duyệt.'
        );
    }
}

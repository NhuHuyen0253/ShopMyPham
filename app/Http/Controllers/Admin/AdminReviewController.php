<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status'); // all | pending | approved | rejected

        $query = Review::with('product')->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function reply(Request $request, Review $review)
    {
        $data = $request->validate([
            'admin_reply' => 'required|string|min:2',
        ]);

        $review->admin_reply = $data['admin_reply'];
        $review->replied_at  = Carbon::now();

        // trả lời thì cho duyệt luôn (tuỳ bạn)
        if ($review->status === 'pending') {
            $review->status = 'approved';
        }

        $review->save();

        return back()->with('success', 'Đã gửi phản hồi cho đánh giá.');
    }

    public function changeStatus(Request $request, Review $review)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->status = $data['status'];
        $review->save();

        return back()->with('success', 'Cập nhật trạng thái đánh giá thành công.');
    }
}

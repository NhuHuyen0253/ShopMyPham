<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $wishlistItems = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle($productId)
    {
        $user = Auth::user();

        $product = Product::findOrFail($productId);

        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($exists) {
            $exists->delete();
            return back()->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Đã thêm sản phẩm vào danh sách yêu thích.');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $item = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function buynow(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $total = $product->price * $quantity;
        return view('orders.buynow', compact('product', 'quantity', 'total'));
    }
    public function confirm(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return redirect('/')->with('error', 'Không tìm thấy sản phẩm để mua!');
        }

        $product = Product::find($productId);

        if (!$product) {
            return redirect('/')->with('error', 'Sản phẩm không tồn tại!');
        }

        return view('order.confirm', compact('product'));
    }

    /**
     * Xử lý khi người dùng bấm “Xác nhận mua”
     */
    public function place(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để mua hàng!');
        }

        // Kiểm tra dữ liệu
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        // Lấy sản phẩm và tính tổng
        $product = Product::find($request->product_id);
        $total = $product->price * $request->quantity;

        // ✅ Tạo đơn hàng mới
        $order = Order::create([
            'user_id' => Auth::id(),
            'status'  => 'pending',
            'total'   => $total,
        ]);

        // ✅ Tạo chi tiết đơn hàng
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => $request->quantity,
            'price'      => $product->price,
        ]);

        // ✅ Sau khi mua xong → chuyển đến trang chi tiết đơn hàng
        return redirect()->route('order.show', ['id' => $order->id])
            ->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');
    }

    /**
     * Hiển thị chi tiết 1 đơn hàng
     */
    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đơn hàng!');
        }

        $order = Order::with('orderItems.product')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return redirect('/')->with('error', 'Không tìm thấy đơn hàng!');
        }

        return view('order.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        // Ở đây bạn có thể lưu đơn hàng vào DB hoặc gọi API thanh toán
        // Order::create([...]);

        return redirect()->route('home')->with('success', 'Đặt hàng thành công!');
    }
}

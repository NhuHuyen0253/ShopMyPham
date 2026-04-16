<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->get('status');
        $q        = trim((string) $request->get('q', ''));
        $from     = $request->get('from');
        $to       = $request->get('to');
        $paidOnly = $request->get('paid');

        $orders = Order::query()
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $onlyNumber = (int) preg_replace('/\D/', '', $q);

                $query->where(function ($sub) use ($q, $onlyNumber) {
                    if ($onlyNumber > 0) {
                        $sub->where('id', $onlyNumber);
                    }

                    $sub->orWhere('receiver_name', 'like', "%{$q}%")
                        ->orWhere('receiver_phone', 'like', "%{$q}%");
                });
            })
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->when($paidOnly !== null && $paidOnly !== '', function ($query) use ($paidOnly) {
                $query->where('is_paid', (int) $paidOnly);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusMap = $this->statusMap();

        return view('admin.orders.index', compact(
            'orders',
            'status',
            'q',
            'from',
            'to',
            'paidOnly',
            'statusMap'
        ));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'user']);
        $statusMap = $this->statusMap();

        return view('admin.orders.show', compact('order', 'statusMap'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'nullable|in:pending,awaiting_payment,processing,shipped,completed,cancelled',
            'note'   => 'nullable|string|max:2000',
        ]);

        try {
            DB::transaction(function () use ($data, $order) {
                $oldStatus = $order->status;
                $newStatus = $data['status'] ?? $oldStatus;

                if (array_key_exists('note', $data)) {
                    $order->admin_note = $data['note'];
                }

                if (in_array($oldStatus, ['pending', 'awaiting_payment'], true) && $newStatus === 'processing') {
                    $this->deductStockForOrder($order);
                }

                if (
                    in_array($oldStatus, ['processing', 'shipped', 'completed'], true) &&
                    in_array($newStatus, ['pending', 'awaiting_payment'], true)
                ) {
                    $this->restoreStockForOrder($order);
                }

                if (
                    in_array($oldStatus, ['processing', 'shipped', 'completed'], true) &&
                    $newStatus === 'cancelled'
                ) {
                    $this->restoreStockForOrder($order);
                }

                $order->status = $newStatus;
                $order->save();
            });

            return back()->with('success', 'Đã cập nhật đơn hàng.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi cập nhật đơn: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                if ((int) $order->stock_deducted === 1) {
                    $this->restoreStockForOrder($order);
                }

                $order->status = 'cancelled';
                $order->save();
            });

            return redirect()->route('admin.orders.index')->with('success', 'Đã hủy đơn.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Lỗi hủy đơn: ' . $e->getMessage());
        }
    }

    public function bulk(Request $request)
    {
        $ids    = (array) $request->input('ids', []);
        $action = (string) $request->input('action', '');

        if (empty($ids) || $action === '') {
            return back()->with('error', 'Vui lòng chọn đơn và thao tác.');
        }

        $allowed = ['pending', 'awaiting_payment', 'processing', 'shipped', 'completed', 'cancelled'];
        if (!in_array($action, $allowed, true)) {
            return back()->with('error', 'Thao tác không hợp lệ.');
        }

        try {
            DB::transaction(function () use ($ids, $action) {
                $orders = Order::whereIn('id', $ids)->lockForUpdate()->get();

                foreach ($orders as $order) {
                    $oldStatus = $order->status;

                    if (in_array($oldStatus, ['pending', 'awaiting_payment'], true) && $action === 'processing') {
                        $this->deductStockForOrder($order);
                    }

                    if (
                        in_array($oldStatus, ['processing', 'shipped', 'completed'], true) &&
                        in_array($action, ['pending', 'awaiting_payment'], true)
                    ) {
                        $this->restoreStockForOrder($order);
                    }

                    if (
                        in_array($oldStatus, ['processing', 'shipped', 'completed'], true) &&
                        $action === 'cancelled'
                    ) {
                        $this->restoreStockForOrder($order);
                    }

                    $order->status = $action;
                    $order->save();
                }
            });

            return back()->with('success', 'Đã cập nhật ' . count($ids) . ' đơn hàng.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi bulk: ' . $e->getMessage());
        }
    }

    public function togglePaid(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'paid' => 'required',
        ]);

        if ($order->payment_method === 'vnpay') {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Đơn VNPay tự cập nhật thanh toán, không tick tay.',
                ], 403);
            }

            return back()->with('error', 'Đơn VNPay tự cập nhật thanh toán, không tick tay.');
        }

        $paid = filter_var($request->input('paid'), FILTER_VALIDATE_BOOLEAN);
        $order->is_paid = $paid ? 1 : 0;
        $order->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'paid'    => (bool) $order->is_paid,
                'orderId' => (int) $order->id,
                'status'  => $order->status,
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }

    public function toggleRefund(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'cancelled' || (int) $order->is_paid !== 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Chỉ hoàn tiền cho đơn đã hủy và đã thanh toán.',
                ], 422);
            }

            return back()->with('error', 'Chỉ hoàn tiền cho đơn đã hủy và đã thanh toán.');
        }

        $data = $request->validate([
            'refunded' => 'required',
            'note'     => 'nullable|string|max:2000',
        ]);

        $refunded = filter_var($data['refunded'], FILTER_VALIDATE_BOOLEAN);

        $order->is_refunded = $refunded ? 1 : 0;
        $order->refunded_at = $refunded ? now() : null;

        if (array_key_exists('note', $data)) {
            $order->refund_note = $data['note'];
        }

        $order->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok'          => true,
                'refunded'    => (bool) $order->is_refunded,
                'refunded_at' => optional($order->refunded_at)->toDateTimeString(),
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái hoàn tiền.');
    }

    public function confirm(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = Order::lockForUpdate()->findOrFail($id);

                if (!in_array($order->status, ['pending', 'awaiting_payment'], true)) {
                    throw new \Exception('Chỉ đơn pending hoặc awaiting_payment mới được xác nhận.');
                }

                $this->deductStockForOrder($order);

                $order->status = 'processing';
                $order->save();
            });

            return back()->with('success', 'Đã xác nhận đơn và cập nhật kho thành công.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi xác nhận đơn: ' . $e->getMessage());
        }
    }

    public function updateShipping(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'shipping_carrier' => 'nullable|string|max:255',
            'shipping_service' => 'nullable|string|max:255',
            'tracking_code'    => 'nullable|string|max:255',
            'shipping_fee'     => 'nullable|integer|min:0',
            'shipping_note'    => 'nullable|string|max:2000',
            'shipped_at'       => 'nullable|date',
        ]);

        $order->fill($data);
        $order->save();

        return back()->with('success', 'Đã cập nhật thông tin giao hàng.');
    }

    public function cancel(int $id)
    {
        $order = Order::findOrFail($id);

        try {
            DB::transaction(function () use ($order) {
                if ((int) $order->stock_deducted === 1) {
                    $this->restoreStockForOrder($order);
                }

                $order->status = 'cancelled';
                $order->save();
            });

            return back()->with('success', 'Đã hủy đơn.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi hủy đơn: ' . $e->getMessage());
        }
    }

    private function deductStockForOrder(Order $order): void
    {
        if ((int) $order->stock_deducted === 1) {
            return;
        }

        $items = OrderItem::where('order_id', $order->id)->get();

        if ($items->isEmpty()) {
            throw new \Exception('Đơn hàng không có sản phẩm để xuất kho.');
        }

        foreach ($items as $item) {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);
            $qty = (int) $item->quantity;

            if ((int) $product->quantity < $qty) {
                throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho.");
            }

            if (empty($product->default_warehouse_id)) {
                throw new \Exception("Sản phẩm {$product->name} chưa có kho mặc định.");
            }

            $product->quantity = (int) $product->quantity - $qty;
            $product->save();

            StockMovement::create([
                'product_id'     => $product->id,
                'warehouse_id'   => $product->default_warehouse_id,
                'type'           => 'out',
                'quantity'       => $qty,
                'unit_cost'      => $product->original_price ?? 0,
                'supplier_id'    => $product->supplier_id,
                'reference_code' => 'ORDER-' . $order->id,
                'note'           => 'Xuất kho cho đơn hàng #' . $order->id,
                'moved_at'       => now(),
            ]);
        }

        $order->stock_deducted = 1;
        $order->save();
    }

    private function restoreStockForOrder(Order $order): void
    {
        if ((int) $order->stock_deducted !== 1) {
            return;
        }

        $items = OrderItem::where('order_id', $order->id)->get();

        if ($items->isEmpty()) {
            throw new \Exception('Đơn hàng không có sản phẩm để hoàn kho.');
        }

        foreach ($items as $item) {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);
            $qty = (int) $item->quantity;

            if (empty($product->default_warehouse_id)) {
                throw new \Exception("Sản phẩm {$product->name} chưa có kho mặc định.");
            }

            $product->quantity = (int) $product->quantity + $qty;
            $product->save();

            StockMovement::create([
                'product_id'     => $product->id,
                'warehouse_id'   => $product->default_warehouse_id,
                'type'           => 'in',
                'quantity'       => $qty,
                'unit_cost'      => $product->original_price ?? 0,
                'supplier_id'    => $product->supplier_id,
                'reference_code' => 'RESTORE-ORDER-' . $order->id,
                'note'           => 'Hoàn kho cho đơn hàng #' . $order->id,
                'moved_at'       => now(),
            ]);
        }

        $order->stock_deducted = 0;
        $order->save();
    }

    private function statusMap(): array
    {
        return [
            'awaiting_payment' => ['Chờ thanh toán', 'primary'],
            'pending'          => ['Chờ xử lý', 'secondary'],
            'processing'       => ['Đang xử lý', 'warning'],
            'shipped'          => ['Đã gửi hàng', 'info'],
            'completed'        => ['Hoàn thành', 'success'],
            'cancelled'        => ['Đã hủy', 'danger'],
        ];
    }
}
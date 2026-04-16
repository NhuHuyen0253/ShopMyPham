@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    @php
        $badge = $statusMap[$order->status] ?? ['Không xác định', 'secondary'];
        $subtotal = (int) $order->orderItems->sum(fn($i) => (int)$i->price * (int)$i->quantity);
        $discount = (int)($order->discount ?? 0);
        $shippingFee = (int)($order->shipping_fee ?? 0);
        $grandTotal = (int)($order->total ?? max(0, $subtotal - $discount + $shippingFee));
        $canConfirm = $order->status === 'pending';
        $canCancel = !in_array($order->status, ['completed', 'cancelled'], true);
        $isPaid = (int)($order->is_paid ?? 0) === 1;
        $isRefunded = (int)($order->is_refunded ?? 0) === 1;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Chi tiết đơn hàng #{{ $order->id }}</h1>
            <p class="admin-page-subtitle">Tạo lúc: {{ optional($order->created_at)->format('d/m/Y H:i') }}</p>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn-admin-light">← Quay lại danh sách</a>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert-danger mb-4">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="admin-alert admin-alert-danger mb-4">
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Tổng quan đơn hàng</h3>

                    <div class="row g-3">
                        <div class="col-sm-6 col-xl-3">
                            <div class="admin-summary-box">
                                <div class="admin-summary-label">Trạng thái đơn</div>
                                <span class="badge bg-{{ $badge[1] }} fs-6 px-3 py-2">{{ $badge[0] }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xl-3">
                            <div class="admin-summary-box">
                                <div class="admin-summary-label">Thanh toán</div>
                                @if($isPaid)
                                    <span class="admin-badge admin-badge-green">Đã thanh toán</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Chưa thanh toán</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6 col-xl-3">
                            <div class="admin-summary-box">
                                <div class="admin-summary-label">Phương thức</div>
                                <div class="fw-bold text-uppercase">{{ $order->payment_method ?? 'cod' }}</div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xl-3">
                            <div class="admin-summary-box">
                                <div class="admin-summary-label">Hoàn tiền</div>
                                @if($isRefunded)
                                    <span class="admin-badge admin-badge-blue">Đã hoàn tiền</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Chưa hoàn tiền</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Sản phẩm trong đơn</h3>

                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-gray-800">{{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}</div>
                                            @if(!empty($item->product?->sku))
                                                <div class="text-sm text-gray-500 mt-1">SKU: {{ $item->product->sku }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">{{ number_format((int)$item->price, 0, ',', '.') }} đ</td>
                                        <td class="text-center">{{ (int)$item->quantity }}</td>
                                        <td class="text-end text-nowrap font-semibold">{{ number_format((int)$item->price * (int)$item->quantity, 0, ',', '.') }} đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">Không có sản phẩm nào trong đơn hàng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end text-gray-500">Tạm tính</td>
                                    <td class="text-end text-nowrap">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end text-gray-500">Giảm giá</td>
                                    <td class="text-end text-nowrap">- {{ number_format($discount, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end text-gray-500">Phí vận chuyển</td>
                                    <td class="text-end text-nowrap">{{ number_format($shippingFee, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng cộng</td>
                                    <td class="text-end fw-bold text-danger text-nowrap">{{ number_format($grandTotal, 0, ',', '.') }} đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Cập nhật đơn hàng</h3>

                    <form action="{{ route('admin.orders.update', ['order' => $order->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="admin-label">Trạng thái</label>
                                <select name="status" class="admin-select">
                                    @foreach($statusMap as $key => $info)
                                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $info[0] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="admin-label">Ghi chú admin</label>
                                <textarea name="note" rows="3" class="admin-textarea">{{ old('note', $order->admin_note) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn-admin-pink">Lưu cập nhật</button>
                        </div>
                    </form>

                    <div class="mt-4 d-flex flex-wrap gap-2">
                        @if($canConfirm)
                            <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST" onsubmit="return confirm('Xác nhận đơn #{{ $order->id }} và trừ kho?');">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-3">Xác nhận đơn & trừ kho</button>
                            </form>
                        @endif

                        @if($canCancel)
                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?');">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-3">Hủy đơn</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thanh toán & hoàn tiền</h3>

                    <div class="d-flex flex-wrap gap-4 align-items-start">
                        <form action="{{ route('admin.orders.paid', $order->id) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="paid" value="{{ $isPaid ? 0 : 1 }}">
                            <button type="submit" class="{{ $isPaid ? 'btn-admin-light' : 'btn-admin-soft-pink' }}">
                                {{ $isPaid ? 'Đánh dấu chưa thanh toán' : 'Đánh dấu đã thanh toán' }}
                            </button>
                        </form>

                        @if($order->status === 'cancelled' && $isPaid)
                            <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" class="row g-2 align-items-end flex-grow-1">
                                @csrf
                                @method('PATCH')

                                <div class="col-auto">
                                    <label class="admin-label">Hoàn tiền</label>
                                    <select name="refunded" class="admin-select">
                                        <option value="1" @selected($isRefunded)>Đã hoàn</option>
                                        <option value="0" @selected(!$isRefunded)>Chưa hoàn</option>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="admin-label">Ghi chú hoàn tiền</label>
                                    <input type="text" name="note" class="admin-input" value="{{ old('note', $order->refund_note) }}">
                                </div>

                                <div class="col-auto">
                                    <button type="submit" class="btn btn-warning rounded-3">Cập nhật hoàn tiền</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    @if($order->refund_note)
                        <div class="mt-3 text-sm text-gray-500">
                            <strong>Ghi chú hoàn tiền:</strong> {{ $order->refund_note }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thông tin khách hàng</h3>

                    <div class="admin-info-block">
                        <div class="admin-info-label">Người nhận</div>
                        <div class="admin-info-value">{{ $order->receiver_name }}</div>
                    </div>

                    <div class="admin-info-block">
                        <div class="admin-info-label">Số điện thoại</div>
                        <div class="admin-info-value">{{ $order->receiver_phone }}</div>
                    </div>

                    <div class="admin-info-block">
                        <div class="admin-info-label">Địa chỉ nhận</div>
                        <div class="admin-info-value">{{ $order->receiver_addr }}</div>
                    </div>

                    @if($order->user)
                        <hr>
                        <div class="admin-info-block">
                            <div class="admin-info-label">Tài khoản</div>
                            <div class="admin-info-value">{{ $order->user->name ?? 'N/A' }}</div>
                            @if(!empty($order->user->email))
                                <div class="text-sm text-gray-500 mt-1">{{ $order->user->email }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thông tin vận chuyển</h3>

                    <form action="{{ route('admin.orders.shipping.update', $order->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="admin-label">Đơn vị vận chuyển</label>
                            <input type="text" name="shipping_carrier" class="admin-input" value="{{ old('shipping_carrier', $order->shipping_carrier) }}">
                        </div>

                        <div class="mb-3">
                            <label class="admin-label">Mã vận đơn</label>
                            <input type="text" name="tracking_code" class="admin-input" value="{{ old('tracking_code', $order->tracking_code) }}">
                        </div>

                        <div class="mb-3">
                            <label class="admin-label">Phí vận chuyển</label>
                            <input type="number" min="0" name="shipping_fee" class="admin-input" value="{{ old('shipping_fee', (int)$order->shipping_fee) }}">
                        </div>

                        <div class="mb-3">
                            <label class="admin-label">Ngày giao / gửi hàng</label>
                            <input type="datetime-local" name="shipped_at" class="admin-input" value="{{ old('shipped_at', $order->shipped_at ? $order->shipped_at->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="admin-label">Ghi chú vận chuyển</label>
                            <textarea name="shipping_note" rows="3" class="admin-textarea">{{ old('shipping_note', $order->shipping_note) }}</textarea>
                        </div>

                        <button type="submit" class="btn-admin-light w-full admin-badge-blue admin-label">Cập nhật vận chuyển</button>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thông tin thêm</h3>


                    <div class="admin-info-row">
                        <span class="admin-info-label">Mã đơn</span>
                        <strong>#{{ $order->id }}</strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="admin-info-label">Đã trừ kho</span>
                        @if((int)($order->stock_deducted ?? 0) === 1)
                            <span class="admin-badge admin-badge-green">Đã trừ</span>
                        @else
                            <span class="admin-badge admin-badge-pink">Chưa trừ</span>
                        @endif
                    </div>

                    @if($order->shipping_note)
                        <div class="mt-3">
                            <div class="admin-info-label mb-1">Ghi chú vận chuyển</div>
                            <div>{{ $order->shipping_note }}</div>
                        </div>
                    @endif
                    
                    @if($order->status === 'cancelled' && !empty($order->refund_note))
                        <div class="mt-3">
                            <div class="admin-info-label mb-1">Lý do khách hủy đơn</div>
                            <div class="p-3 rounded-3 bg-light border">
                                {{ $order->refund_note }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
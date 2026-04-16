@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <h1 class="admin-page-title">Quản lý đơn hàng</h1>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="admin-label">Trạng thái</label>
                    <select name="status" class="admin-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($statusMap as $key => $meta)
                            <option value="{{ $key }}" @selected(($status ?? '') === $key)>{{ $meta[0] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="admin-label">Tìm kiếm</label>
                    <input type="text" name="q" class="admin-input" value="{{ $q }}" placeholder="Mã đơn / Tên / SĐT">
                </div>

                <div class="col-md-2">
                    <label class="admin-label">Từ ngày</label>
                    <input type="date" name="from" class="admin-input" value="{{ $from }}">
                </div>

                <div class="col-md-2">
                    <label class="admin-label">Đến ngày</label>
                    <input type="date" name="to" class="admin-input" value="{{ $to }}">
                </div>

                <div class="col-md-2">
                    <label class="admin-label">Thanh toán</label>
                    <select name="paid" class="admin-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" @selected(($paidOnly ?? '') === '1')>Đã thanh toán</option>
                        <option value="0" @selected(($paidOnly ?? '') === '0')>Chưa thanh toán</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button class="btn-admin-pink w-full">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select id="bulk-action" class="admin-select" style="max-width:240px">
                    <option value="">— Thao tác hàng loạt —</option>
                    <option value="processing">Chuyển “Đang xử lý”</option>
                    <option value="shipped">Chuyển “Đã gửi hàng”</option>
                    <option value="completed">Chuyển “Hoàn tất”</option>
                    <option value="cancelled">Chuyển “Đã hủy”</option>
                </select>

                <button type="button" class="btn-admin-light" onclick="submitBulk()">Áp dụng</button>
            </div>
        </div>
    </div>

    <form id="bulk-form" method="POST" action="{{ route('admin.orders.bulk') }}" class="d-none">
        @csrf
        <input type="hidden" name="action" id="bulk-action-hidden">
        <div id="bulk-ids"></div>
    </form>

    <div class="admin-table-wrap">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="chk-all"></th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Liên hệ</th>
                        <th>Tổng tiền</th>
                        <th>PTTT</th>
                        <th>Thanh toán</th>
                        <th>Hoàn tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $badge = $statusMap[$order->status] ?? ['Chờ xác nhận', 'secondary'];
                            $pm = $order->payment_method ?? 'cod';
                            $canRefundUI = ($order->status === 'cancelled' && (int)$order->is_paid === 1);
                            $isRefunded  = (int)($order->is_refunded ?? 0) === 1;
                        @endphp

                        <tr>
                            <td><input type="checkbox" class="chk-row" value="{{ $order->id }}"></td>
                            <td class="font-semibold">#{{ $order->id }}</td>
                            <td>{{ $order->receiver_name ?? $order->user?->name ?? 'Khách' }}</td>

                            <td>
                                <div>{{ $order->receiver_phone }}</div>
                                <div class="text-muted text-sm">{{ $order->receiver_addr }}</div>
                            </td>

                            <td class="text-danger fw-bold text-nowrap">{{ number_format((int)$order->total, 0, ',', '.') }} đ</td>

                            <td>
                                @if($pm === 'vnpay')
                                    <span class="admin-badge admin-badge-blue">VNPay</span>
                                @elseif($pm === 'cod')
                                    <span class="admin-badge admin-badge-gray">COD</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">{{ strtoupper($pm) }}</span>
                                @endif
                            </td>

                            <td>
                                @if((int)$order->is_paid === 1)
                                    <span class="admin-badge admin-badge-green">Đã TT</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Chưa TT</span>
                                @endif
                            </td>

                            <td>
                                @if($canRefundUI)
                                    @if($isRefunded)
                                        <span class="admin-badge admin-badge-green">Đã hoàn</span>
                                    @else
                                        <span class="admin-badge admin-badge-yellow">Chờ hoàn</span>
                                    @endif
                                @else
                                    <span class="text-muted text-sm">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge[1] }}">{{ $badge[0] }}</span>
                            </td>

                            <td class="text-nowrap">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>

                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.orders.show', $order) }}" class=" admin-badge admin-badge-gray ">Chi tiết</a>

                                    @if($order->status === 'pending')
                                        <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST" class="js-confirm-form" data-confirm="Xác nhận đơn này và trừ kho?">
                                            @csrf
                                            <button type="submit" class="admin-badge admin-badge-orange">Xác nhận đơn</button>
                                        </form>
                                    @endif

                                    @if($pm !== 'vnpay')
                                        @if((int)$order->is_paid === 1)
                                            <button type="button" class="admin-badge admin-badge-yellow" onclick="togglePaid({{ $order->id }}, false)">Bỏ thanh toán</button>
                                        @else
                                            <button type="button" class="admin-badge admin-badge-blue" onclick="togglePaid({{ $order->id }}, true)">Đánh dấu đã thanh toán</button>
                                        @endif
                                    @endif

                                    @if($canRefundUI)
                                        @if($isRefunded)
                                            <button type="button" class="admin-badge admin-badge-pink" onclick="toggleRefund({{ $order->id }}, false)">Bỏ hoàn</button>
                                        @else
                                            <button type="button" class="admin-action-btn edit" onclick="toggleRefund({{ $order->id }}, true)">Đã hoàn tiền</button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-gray-500">Không có đơn phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

<div id="orders-root"
     data-paid-url-template="{{ route('admin.orders.paid', ['id' => '__ID__']) }}"
     data-refund-url-template="{{ route('admin.orders.refund', ['id' => '__ID__']) }}"
     data-csrf="{{ csrf_token() }}">
</div>

<script src="{{ asset('js/order.js') }}?v={{ file_exists(public_path('js/order.js')) ? filemtime(public_path('js/order.js')) : time() }}" defer></script>
@endsection
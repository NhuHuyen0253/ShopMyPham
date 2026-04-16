@extends('admin.layout')

@php
    $statusLabels = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'success' => 'Thành công',
    ];
@endphp

@section('content')
<div class="p-4 admin-page admin-customer-detail-page">

    {{-- Top bar --}}
    <div class="admin-page-header mb-4">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="admin-back-link admin-badge admin-badge-gray">
                ← Quay lại danh sách
            </a>
            <h1 class="admin-page-title mt-2 mb-0">Chi tiết khách hàng</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Thông tin khách hàng --}}
        <div class="admin-card lg:col-span-1">
            <div class="admin-card-body">
                <div class="admin-customer-profile">
                    <div class="admin-customer-profile-avatar">
                        {{ strtoupper(mb_substr($customer->name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <div class="admin-customer-profile-name">{{ $customer->name ?? '—' }}</div>
                        <div class="admin-customer-profile-id">Khách hàng #{{ $customer->id }}</div>
                    </div>
                </div>

                <h2 class="admin-section-title mt-4 mb-3">Thông tin khách hàng</h2>

                <div class="admin-info-list">
                    <div class="admin-info-row">
                        <div class="admin-info-label">ID</div>
                        <div class="admin-info-value">#{{ $customer->id }}</div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Tên</div>
                        <div class="admin-info-value">{{ $customer->name ?? '—' }}</div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Email</div>
                        <div class="admin-info-value">{{ $customer->email ?? '—' }}</div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">SĐT</div>
                        <div class="admin-info-value">{{ $customer->phone ?? '—' }}</div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Trạng thái</div>
                        <div class="admin-info-value">
                            @if($customer->is_blocked)
                                <span class="admin-badge admin-badge-red">Đã chặn</span>
                            @else
                                <span class="admin-badge admin-badge-green">Hoạt động</span>
                            @endif
                        </div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Đơn hàng</div>
                        <div class="admin-info-value">{{ number_format($customer->orders_count ?? 0) }}</div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Tổng chi tiêu</div>
                        <div class="admin-info-value admin-money">
                            {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}đ
                        </div>
                    </div>

                    <div class="admin-info-row">
                        <div class="admin-info-label">Tham gia</div>
                        <div class="admin-info-value">
                            {{ optional($customer->created_at)->format('d/m/Y') ?? '—' }}
                        </div>
                    </div>
                </div>

                <div class="admin-detail-actions mt-4">
                    <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="admin-action-btn edit"
                            onclick="return confirm('Xác nhận {{ $customer->is_blocked ? 'mở khóa' : 'chặn' }} khách hàng này?')">
                            {{ $customer->is_blocked ? 'Mở khóa' : 'Chặn' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="admin-action-btn delete"
                            onclick="return confirm('Bạn chắc chắn xóa khách hàng này?')">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tất cả đơn hàng --}}
        <div class="admin-card lg:col-span-2">
            <div class="admin-card-body p-0">
                <div class="admin-table-toolbar">
                    <div class="admin-table-title">Tất cả đơn hàng</div>
                    <div class="admin-table-count">
                        Tổng:
                        <strong>
                            {{ method_exists($orders, 'total') ? number_format($orders->total()) : count($orders) }}
                        </strong>
                        đơn hàng
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Đã thanh toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr>
                                    <td class="font-semibold text-gray-800">#{{ $o->id }}</td>
                                    <td>{{ optional($o->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="admin-money">{{ number_format($o->total ?? 0, 0, ',', '.') }}đ</td>

                                    <td class="order-status">
                                        @if(in_array($o->status, ['success', 'completed']))
                                            <span class="admin-badge admin-badge-green">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @elseif($o->status === 'pending')
                                            <span class="admin-badge admin-badge-yellow">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @elseif($o->status === 'processing')
                                            <span class="admin-badge admin-badge-blue">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @elseif($o->status === 'shipped')
                                            <span class="admin-badge admin-badge-pink">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @elseif($o->status === 'cancelled')
                                            <span class="admin-badge admin-badge-red">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @else
                                            <span class="admin-badge admin-badge-gray">
                                                {{ $statusLabels[$o->status] ?? $o->status }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <label class="admin-paid-check">
                                            <input
                                                type="checkbox"
                                                class="paid-toggle"
                                                data-url="{{ route('admin.orders.togglePaid', $o) }}"
                                                {{ !empty($o->is_paid) && $o->is_paid ? 'checked' : '' }}
                                            >
                                            <span>{{ !empty($o->is_paid) && $o->is_paid ? 'Đã trả' : 'Chưa trả' }}</span>
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-6">
                                        Chưa có đơn hàng.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($orders, 'links'))
                    <div class="admin-table-footer">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/customers.js') }}" defer></script>
@endsection
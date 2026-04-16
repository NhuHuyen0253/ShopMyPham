@extends('admin.layout')

@section('content')
@php
    $today = \Carbon\Carbon::today()->toDateString();
@endphp

<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Trang chủ Admin</h1>
        </div>

        <div class="d-flex gap-2">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-admin-pink">🚪Đăng xuất</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="admin-stat-card">
            <div class="admin-stat-label">📦Tổng sản phẩm</div>
            <div class="admin-stat-value pink">{{ $totalProducts }}</div>
        </div>

        <a href="{{ route('admin.orders.index', ['from' => $today, 'to' => $today]) }}" class="admin-stat-card block">
            <div class="admin-stat-label">🛒Đơn hàng hôm nay</div>
            <div class="admin-stat-value blue">{{ $todayOrders }}</div>
        </a>

        <div class="admin-stat-card">
            <div class="admin-stat-label">👤Khách hàng</div>
            <div class="admin-stat-value green">{{ $totalUsers }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Đơn hàng mới --}}
        <div class="admin-card">
            <div class="admin-card-body">
                <h3 class="admin-section-title">📦 Thông báo đơn hàng mới</h3>

                <div class="space-y-3">
                    @forelse ($newOrders as $order)
                        <div class="admin-alert admin-alert-success">
                            📦 Đơn hàng mới từ <strong>{{ $order->user->name ?? 'Khách chưa đăng ký' }}</strong>.
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm">Không có thông báo đơn hàng mới.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $newOrders->appends(request()->except('orders_page'))->links() }}
                </div>
            </div>
        </div>

        {{-- Sản phẩm mới --}}
        <div class="admin-card">
            <div class="admin-card-body">
                <h3 class="admin-section-title">🎉 Thông báo sản phẩm mới</h3>

                <div class="space-y-3">
                    @forelse ($newProducts as $product)
                        <div class="admin-alert admin-alert-info">
                            🎉 Sản phẩm mới: <strong>{{ $product->name }}</strong> đã được thêm.
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm">Không có thông báo sản phẩm mới.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $newProducts->appends(request()->except('products_page'))->links() }}
                </div>
            </div>
        </div>

        {{-- Sắp hết hàng --}}
        <div class="admin-card">
            <div class="admin-card-body">
                <h3 class="admin-section-title">⚠️ Thông báo sắp hết hàng</h3>

                <div class="space-y-3">
                    @forelse ($lowStock as $product)
                        <div class="admin-alert admin-alert-warning">
                            ⚠️ <strong>{{ $product->name }}</strong> sắp hết hàng (còn {{ $product->quantity }}).
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm">Không có sản phẩm sắp hết hàng.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $lowStock->appends(request()->except('lowstock_page'))->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
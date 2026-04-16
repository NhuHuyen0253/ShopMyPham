@extends('layout')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    
                    <ul class="list-group list-group-flush account-side-menu">
                        <li class="list-group-item {{ request()->routeIs('profile.info') ? 'active' : '' }}">
                            <a href="{{ route('profile.info') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-user"></i>
                                <span>Thông tin tài khoản</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-box"></i>
                                <span>Đơn hàng của tôi</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-heart"></i>
                                <span>Danh sách yêu thích</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.rebuy') ? 'active' : '' }}">
                            <a href="{{ route('profile.rebuy') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Mua lại</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.faq') ? 'active' : '' }}">
                            <a href="{{ route('profile.faq') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-circle-question"></i>
                                <span>Hỏi đáp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="p-4 p-md-5 bg-white">
                    <n class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark">Đơn hàng của tôi</h4>
                        </div>
                        </n>
                        <form method="GET" action="{{ route('profile.orders') }}" class="w-100 w-lg-auto">
                            <div class="row g-2">
                                <div class="col-12 col-md">
                                    <input
                                        type="text"
                                        name="q"
                                        value="{{ $q }}"
                                        class="form-control"
                                        placeholder="Tìm mã đơn (ID)">
                                </div>

                                <div class="col-12 col-md-auto">
                                    <select name="status" class="form-select" style="min-width: 190px;">
                                        <option value="">-- Tất cả trạng thái --</option>
                                        @foreach($statusMap as $key => $meta)
                                            <option value="{{ $key }}" @selected($status === $key)>{{ $meta[0] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <button class="btn btn-pink w-100 px-4">Lọc</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($orders->count() === 0)
                        <div class="border rounded-4 p-4 text-center bg-light-subtle">
                            <p class="text-muted mb-3">Chưa có đơn hàng nào.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-pink px-4 rounded-pill">
                                Mua sắm ngay
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle order-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày tạo</th>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        @php
                                            $item  = $order->orderItems->first();
                                            $qty   = $order->orderItems->sum('quantity');
                                            $thumb = $item?->product?->image
                                                ? asset('images/product/' . ltrim($item->product->image, '/'))
                                                : asset('images/avatar-placeholder.jpg');
                                            $badge = $statusMap[$order->status] ?? ['Khác', 'secondary'];
                                        @endphp

                                        <tr>
                                            <td class="fw-semibold">#{{ $order->id }}</td>
                                            <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img
                                                        src="{{ $thumb }}"
                                                        alt=""
                                                        class="rounded-3 border"
                                                        style="width: 52px; height: 52px; object-fit: cover;"
                                                    >
                                                    <div>
                                                        <div class="fw-semibold text-dark">
                                                            {{ \Illuminate\Support\Str::limit($item?->product?->name ?? 'Sản phẩm', 42) }}
                                                        </div>
                                                        @if($order->orderItems->count() > 1)
                                                            <div class="text-muted small">
                                                                +{{ $order->orderItems->count() - 1 }} sản phẩm khác
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $qty }}</td>
                                            <td class="text-danger fw-bold">
                                                {{ number_format((int) $order->total, 0, ',', '.') }} đ
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $badge[1] }} px-3 py-2">
                                                    {{ $badge[0] }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                                    <a href="{{ route('order.showorder', ['id' => $order->id]) }}"
                                                       class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                        Chi tiết
                                                    </a>

                                                    <a href="{{ route('order.notice', ['id' => $order->id]) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        Thông báo
                                                    </a>

                                                    @if($order->status === 'awaiting_payment')
                                                        <a href="{{ route('order.confirm', ['id' => $order->id]) }}"
                                                           class="btn btn-sm btn-warning rounded-pill px-3 text-white">
                                                            Thanh toán
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $orders->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
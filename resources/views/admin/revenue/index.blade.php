@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-revenue-page">

    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Quản lý doanh thu</h1>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.revenue.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="admin-label">Từ ngày</label>
                        <input type="date" name="from" class="admin-input" value="{{ $from }}">
                    </div>

                    <div>
                        <label class="admin-label">Đến ngày</label>
                        <input type="date" name="to" class="admin-input" value="{{ $to }}">
                    </div>

                    <div>
                        <button type="submit" class="btn-admin-pink w-full">Lọc</button>
                    </div>

                    <div>
                        <a href="{{ route('admin.revenue.index') }}" class="btn-admin-light w-full inline-flex justify-center">
                            Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Tổng doanh thu</div>
            <div class="admin-stat-value pink">{{ number_format((int)$totalRevenue, 0, ',', '.') }} đ</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-label">Doanh thu hôm nay</div>
            <div class="admin-stat-value blue">{{ number_format((int)$todayRevenue, 0, ',', '.') }} đ</div>
        </div>
          <div class="admin-stat-card">
            <div class="admin-stat-label">Doanh thu tháng này</div>
            <div class="admin-stat-value orange">{{ number_format((int)$monthRevenue, 0, ',', '.') }} đ</div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Số đơn hoàn tất</div>
            <div class="admin-stat-value">{{ number_format((int)$totalOrders) }}</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-label">Số đơn hủy</div>
            <div class="admin-stat-value">{{ number_format((int)$cancelledCount) }}</div>
        </div>
    </div>
        

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 mb-4">
        <div class="xl:col-span-8 space-y-5">
        <div class="admin-card admin-chart-card">
            <div class="admin-card-body">
                <div class="admin-chart-header mb-4">
                    <div>
                        <div class="admin-section-title mb-1">Biểu đồ doanh thu theo ngày</div>
                        <div class="admin-chart-subtitle">
                            Theo dõi biến động doanh thu từng ngày trong khoảng thời gian đã chọn.
                        </div>
                    </div>
                    <span class="admin-badge admin-badge-blue">Theo ngày</span>
                </div>

                <div class="admin-chart-box admin-chart-box-lg">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-card admin-chart-card">
                <div class="admin-card-body">
                    <div class="admin-chart-header mb-4">
                        <div>
                            <div class="admin-section-title mb-1">Biểu đồ doanh thu theo tháng</div>
                            <div class="admin-chart-subtitle">
                                Tổng quan xu hướng doanh thu theo từng tháng.
                            </div>
                        </div>
                        <span class="admin-badge admin-badge-blue">Theo tháng</span>
                    </div>

                    <div class="admin-chart-box admin-chart-box-lg">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

       <div class="xl:col-span-4">
            <div class="admin-card h-full">
                <div class="admin-card-body">
                    <div class="admin-top-product-header mb-3">
                        <div class="admin-section-title mb-0">Top sản phẩm bán chạy</div>
                        <div class="admin-top-product-subtitle">Top 5 sản phẩm có doanh số tốt nhất</div>
                    </div>

                    @forelse($topProducts as $index => $item)
                        <div class="admin-top-product-card">
                            <div class="admin-top-product-left">
                                <div class="admin-top-rank admin-badge admin-badge-pink">
                                    #{{ $index + 1 }}
                                </div>

                                <div class="admin-top-product-content">
                                    <div class="admin-top-product-name">
                                        {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                    </div>

                                    <div class="admin-top-product-meta">
                                        <span class="admin-top-product-badge">
                                            Đã bán: {{ $item->total_qty }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-top-product-revenue">
                                {{ number_format((int)$item->total_revenue, 0, ',', '.') }} đ
                            </div>
                        </div>
                    @empty
                        <div class="admin-top-product-empty">
                            Chưa có dữ liệu.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách đơn hàng --}}
    <div class="admin-table-wrap">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">Danh sách đơn hàng tạo doanh thu</div>
            <div class="admin-table-count">
                Tổng:
                <strong>{{ method_exists($orders, 'total') ? number_format($orders->total()) : count($orders) }}</strong>
                đơn hàng
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->receiver_name }}</td>
                            <td>{{ $order->receiver_phone }}</td>
                            <td class="admin-money">
                                {{ number_format((int)$order->total, 0, ',', '.') }} đ
                            </td>
                            <td>
                                @if((int)$order->is_paid === 1)
                                    <span class="admin-badge admin-badge-green">Đã thanh toán</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Chưa thanh toán</span>
                                @endif
                            </td>
                            <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="admin-badge-blue admin-badge">
                                        Chi tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-6">Không có dữ liệu doanh thu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-table-footer">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>
<div id="revenueChartData"
     data-daily-labels='@json($dailyRevenue->pluck("day"))'
     data-daily-values='@json($dailyRevenue->pluck("revenue"))'
     data-monthly-labels='@json($monthlyRevenue->pluck("month"))'
     data-monthly-values='@json($monthlyRevenue->pluck("revenue"))'>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/revenue.js') }}" defer></script>
@endsection
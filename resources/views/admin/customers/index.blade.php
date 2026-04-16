@extends('admin.layout')

@section('content')
@php
    $q = request('q');

    function hlt($text, $q) {
        if (!$q || $text === null) return e($text);
        $pattern = '/(' . preg_quote($q, '/') . ')/iu';
        return preg_replace($pattern, '<mark class="admin-highlight">$1</mark>', e($text));
    }
@endphp

<div class="p-4 admin-page admin-customer-page">

    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Khách hàng</h1>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <div class="admin-search-wrap">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Nhập họ tên, email hoặc số điện thoại..."
                        class="admin-search-input"
                    />
                    <button type="submit" class="btn-admin-pink admin-search-btn">
                        Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="admin-filter-grid">
                <input type="hidden" name="q" value="{{ request('q') }}">

                <div>
                    <label class="admin-label">Trạng thái</label>
                    <select name="status" class="admin-select">
                        <option value="">Tất cả</option>
                        <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                        <option value="blocked" @selected(request('status') === 'blocked')>Đã chặn</option>
                    </select>
                </div>

                <div>
                    <label class="admin-label">Sắp xếp</label>
                    <select name="sort" class="admin-select">
                        <option value="latest" @selected(request('sort') === 'latest' || !request()->has('sort'))>Mới nhất</option>
                        <option value="name" @selected(request('sort') === 'name')>Theo tên (A → Z)</option>
                        <option value="orders" @selected(request('sort') === 'orders')>Đơn hàng nhiều nhất</option>
                        <option value="spent" @selected(request('sort') === 'spent')>Chi tiêu cao nhất</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="btn-admin-pink">Lọc dữ liệu</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn-admin-light">Xóa bộ lọc</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-table-wrap">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">Danh sách khách hàng</div>
            <div class="admin-table-count">
                Tổng: <strong>{{ method_exists($customers, 'total') ? number_format($customers->total()) : $customers->count() }}</strong> khách hàng
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $c)
                        @php
                            $isHit = $q && (
                                (isset($c->name) && mb_stripos($c->name, $q) !== false) ||
                                (isset($c->email) && mb_stripos($c->email, $q) !== false) ||
                                (isset($c->phone) && mb_stripos($c->phone, $q) !== false)
                            );
                        @endphp

                        <tr class="{{ $isHit ? 'admin-row-hit hit' : '' }}">
                            <td>
                                <div class="admin-customer-main">
                                    <div class="admin-customer-avatar">
                                        {{ strtoupper(mb_substr($c->name ?? 'K', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="admin-customer-name">{!! hlt($c->name ?? '—', $q) !!}</div>
                                        <div class="admin-customer-id">#{{ $c->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{!! hlt($c->email ?? '—', $q) !!}</td>
                            <td>{!! hlt($c->phone ?? '—', $q) !!}</td>
                            <td>{{ number_format($c->orders_count ?? 0) }}</td>
                            <td class="admin-money">{{ number_format($c->total_spent ?? 0, 0, ',', '.') }}đ</td>

                            <td>
                                @if($c->is_blocked)
                                    <span class="admin-badge admin-badge-red">Đã chặn</span>
                                @else
                                    <span class="admin-badge admin-badge-green">Hoạt động</span>
                                @endif
                            </td>

                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.customers.show', $c) }}" class="admin-badge admin-badge-pink ">
                                        Xem
                                    </a>

                                    <form method="POST" action="{{ route('admin.customers.toggle', $c) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="admin-action-btn edit"
                                            onclick="return confirm('Xác nhận {{ $c->is_blocked ? 'mở khóa' : 'chặn' }} khách hàng này?')">
                                            {{ $c->is_blocked ? 'Mở khóa' : 'Chặn' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.customers.destroy', $c) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="admin-action-btn delete"
                                            onclick="return confirm('Bạn chắc chắn xóa khách hàng này? Hành động không thể hoàn tác.')">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">
                                Không có khách hàng nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-table-footer">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>
</div>

<script src="{{ asset('js/customers.js') }}" defer></script>
@endsection
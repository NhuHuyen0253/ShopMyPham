@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Quản lý chương trình khuyến mãi</h1>
            
        </div>

        <a href="{{ route('admin.promotions.create') }}" class="btn btn-admin-pink admin-header-btn">
            <i class="fas fa-plus"></i>
            <span>Thêm chương trình</span>
        </a>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-table-toolbar">
            <div>
                <div class="admin-table-title">Danh sách khuyến mãi</div>
                <div class="admin-table-count">
                    Tổng cộng {{ $promotions->total() }} chương trình
                </div>
            </div>
        </div>

        <div class="admin-table-wrap border-0 shadow-none rounded-0">
            <div class="table-responsive">
                <table class="admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="70" class="text-center">STT</th>
                            <th>Tên chương trình</th>
                            <th class="text-center">Giảm giá</th>
                            <th class="text-center">Sản phẩm áp dụng</th>
                            <th width="180" class="text-center">Thời gian</th>
                            <th width="140" class="text-center">Trạng thái</th>
                            <th width="170" class="text-end">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($promotions as $promo)
                        @php
                            $today = now()->startOfDay();
                            $startDate = \Carbon\Carbon::parse($promo->start_date)->startOfDay();
                            $endDate = \Carbon\Carbon::parse($promo->end_date)->startOfDay();

                            $isRunning = $promo->is_active && $startDate <= $today && $endDate >= $today;
                            $isUpcoming = $promo->is_active && $startDate > $today;
                            $isExpired = $endDate < $today;
                        @endphp

                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $promotions->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $promo->name }}</div>
                                <div class="text-muted small">Mã chương trình #{{ $promo->id }}</div>

                                @if(!empty($promo->description))
                                    <div class="text-muted small mt-1" style="max-width: 380px; line-height: 1.5;">
                                        {{ \Illuminate\Support\Str::limit($promo->description, 100) }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="fw-bold text-danger">
                                    -{{ (int) ($promo->discount_percent ?? 0) }}%
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="fw-semibold">
                                    {{ method_exists($promo, 'products') ? $promo->products->count() : 0 }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="fw-semibold">{{ $startDate->format('d/m/Y') }}</div>
                                <div class="text-muted small my-1">đến</div>
                                <div class="fw-semibold">{{ $endDate->format('d/m/Y') }}</div>
                            </td>

                            <td class="text-center">
                                @if($isRunning)
                                    <span class="admin-badge admin-badge-green">Đang hiển thị</span>
                                @elseif($isUpcoming)
                                    <span class="admin-badge admin-badge-yellow">Sắp diễn ra</span>
                                @elseif($promo->is_active && $isExpired)
                                    <span class="admin-badge admin-badge-red">Hết hạn</span>
                                @elseif($promo->is_active)
                                    <span class="admin-badge admin-badge-blue">Đã bật</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Đã tắt</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.promotions.edit', $promo) }}"
                                       class="admin-action-btn edit">
                                        <i class="fas fa-pen me-1"></i>Sửa
                                    </a>

                                    <form action="{{ route('admin.promotions.destroy', $promo) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn chắc chắn muốn xóa chương trình này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-btn delete">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-3"
                                         style="width:72px;height:72px;border-radius:50%;background:#fdf2f8;color:#db2777;display:flex;align-items:center;justify-content:center;font-size:28px;">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Chưa có chương trình khuyến mãi nào</h5>
                                    <p class="text-muted mb-3">
                                        Hãy thêm chương trình đầu tiên để bắt đầu quản lý ưu đãi.
                                    </p>
                                    <a href="{{ route('admin.promotions.create') }}" class="btn btn-admin-pink">
                                        <i class="fas fa-plus me-2"></i>Thêm chương trình
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($promotions->hasPages())
            <div class="admin-table-footer d-flex justify-content-end">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
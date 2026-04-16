@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Banner khuyến mãi</h1>
        </div>

        <a href="{{ route('admin.promo-banners.create') }}" class="btn btn-admin-pink admin-header-btn">
            <i class="fas fa-plus"></i>
            <span>Thêm banner</span>
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
                <div class="admin-table-title">Danh sách banner</div>
                <div class="admin-table-count">
                    Tổng cộng {{ $banners->total() }} banner
                </div>
            </div>
        </div>

        <div class="admin-table-wrap border-0 shadow-none rounded-0">
            <div class="table-responsive">
                <table class="admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="70" class="text-center">STT</th>
                            <th>Tên banner</th>
                            <th>Tiêu đề</th>
                            <th>Chương trình</th>
                            <th class="text-center">Giảm giá</th>
                            <th class="text-center">Bắt đầu</th>
                            <th class="text-center">Kết thúc</th>
                            <th class="text-center">Hiển thị</th>
                            <th class="text-center">Thứ tự</th>
                            <th width="170" class="text-end">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($banners as $banner)
                        @php
                            $startAt = $banner->start_at;
                            $endAt = $banner->end_at;
                            $now = now();

                            $isRunning = $banner->is_active
                                && (!$startAt || $startAt <= $now)
                                && (!$endAt || $endAt >= $now);

                            $isUpcoming = $banner->is_active && $startAt && $startAt > $now;
                            $isExpired = $endAt && $endAt < $now;
                        @endphp

                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $banners->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $banner->name }}</div>
                                <div class="text-muted small">ID banner #{{ $banner->id }}</div>
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ \Illuminate\Support\Str::limit($banner->headline, 50) }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $banner->promotion->name ?? '—' }}
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="fw-semibold text-danger">
                                    @if($banner->promotion && !empty($banner->promotion->discount_percent))
                                        -{{ (int) $banner->promotion->discount_percent }}%
                                    @else
                                        {{ $banner->discount_text ?: '—' }}
                                    @endif
                                </span>
                            </td>

                            <td class="text-center">
                                {{ $startAt ? $startAt->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="text-center">
                                {{ $endAt ? $endAt->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="text-center">
                                @if($isRunning)
                                    <span class="admin-badge admin-badge-green">Đang hiển thị</span>
                                @elseif($isUpcoming)
                                    <span class="admin-badge admin-badge-yellow">Sắp hiển thị</span>
                                @elseif($banner->is_active && $isExpired)
                                    <span class="admin-badge admin-badge-red">Đã hết hạn</span>
                                @elseif($banner->is_active)
                                    <span class="admin-badge admin-badge-blue">Đã bật</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Ẩn</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="fw-semibold">{{ $banner->sort_order }}</span>
                            </td>

                            <td class="text-end">
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.promo-banners.edit', $banner) }}"
                                       class="admin-action-btn edit">
                                        <i class="fas fa-pen me-1"></i>Sửa
                                    </a>

                                    <form action="{{ route('admin.promo-banners.destroy', $banner) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Xóa banner này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-action-btn delete" type="submit">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-3"
                                         style="width:72px;height:72px;border-radius:50%;background:#fdf2f8;color:#db2777;display:flex;align-items:center;justify-content:center;font-size:28px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Chưa có banner nào</h5>
                                    <p class="text-muted mb-3">
                                        Hãy thêm banner đầu tiên để bắt đầu hiển thị khuyến mãi trên website.
                                    </p>
                                    <a href="{{ route('admin.promo-banners.create') }}" class="btn btn-admin-pink">
                                        <i class="fas fa-plus me-2"></i>Thêm banner
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($banners->hasPages())
            <div class="admin-table-footer d-flex justify-content-end">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
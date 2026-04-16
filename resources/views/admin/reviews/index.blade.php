@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Đánh giá sản phẩm</h1>
        </div>

        <form method="GET" class="review-filter-form d-flex flex-wrap align-items-center gap-2">
            <select name="status" class="form-select review-filter-select">
                <option value="">Tất cả trạng thái</option>
                <option value="pending"  {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
            <button class="btn btn-light border review-filter-btn" type="submit">
                <i class="fas fa-filter me-2"></i>Lọc
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="review-list">
        @forelse($reviews as $r)
            @php
                $statusMap = [
                    'pending' => ['Chờ duyệt', 'admin-badge-yellow'],
                    'approved' => ['Đã duyệt', 'admin-badge-green'],
                    'rejected' => ['Từ chối', 'admin-badge-red'],
                ];

                [$statusText, $statusClass] = $statusMap[$r->status] ?? ['Không xác định', 'admin-badge-gray'];
            @endphp

            <div class="admin-card review-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <h5 class="mb-0 fw-bold text-dark">{{ $r->customer_name }}</h5>
                                <span class="text-muted small">{{ $r->customer_email }}</span>
                                <span class="admin-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>

                            <div class="review-meta text-muted small mb-3">
                                <span>{{ $r->created_at->format('d/m/Y H:i') }}</span>
                                <span class="mx-2">•</span>
                                <span>Sản phẩm: <strong>{{ $r->product->name ?? '-' }}</strong></span>
                            </div>

                            <div class="review-content">
                                {{ $r->content }}
                            </div>
                        </div>

                        <div class="review-side text-lg-end">
                            <div class="review-stars mb-2" title="{{ $r->rating }}/5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $r->rating ? 'filled' : '' }}">
                                        {{ $i <= $r->rating ? '★' : '☆' }}
                                    </span>
                                @endfor
                            </div>

                            <form method="POST" action="{{ route('admin.reviews.status', $r) }}">
                                @csrf
                                <select name="status"
                                        class="form-select form-select-sm review-status-select"
                                        onchange="this.form.submit()">
                                    <option value="pending"  {{ $r->status === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="approved" {{ $r->status === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="rejected" {{ $r->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="review-reply-box mt-4">
                        <form method="POST" action="{{ route('admin.reviews.reply', $r) }}">
                            @csrf

                            <label class="review-reply-label">
                                <i class="fas fa-reply me-2 text-primary"></i>Phản hồi tới khách
                            </label>

                            <textarea name="admin_reply"
                                      rows="3"
                                      class="form-control review-reply-textarea"
                                      placeholder="Nhập phản hồi của bạn...">{{ old('admin_reply', $r->admin_reply) }}</textarea>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                <button class="btn btn-admin-pink px-4 rounded-pill" type="submit">
                                    <i class="fas fa-paper-plane me-2"></i>Lưu phản hồi
                                </button>

                                @if($r->admin_reply)
                                    <span class="text-muted small">
                                        Đã trả lời: {{ optional($r->replied_at ?? $r->updated_at)->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="admin-card">
                <div class="card-body py-5 text-center">
                    <div class="empty-review-icon mb-3">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Chưa có đánh giá nào</h5>
                    <p class="text-muted mb-0">
                        Khi khách hàng gửi đánh giá sản phẩm, danh sách sẽ hiển thị tại đây.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
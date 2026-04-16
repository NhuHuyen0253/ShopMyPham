@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Sửa banner khuyến mãi</h1>
        </div>

        <a href="{{ route('admin.promo-banners.index') }}" class="btn btn-light border rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <div class="fw-bold mb-2">
                <i class="fas fa-exclamation-circle me-2"></i>Vui lòng kiểm tra lại thông tin
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('admin.promo-banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-12">
                        <div class="admin-section-title mb-3">Thông tin banner</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên nội bộ <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="name"
                            class="form-control rounded-4"
                            value="{{ old('name', $banner->name) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tiêu đề chính <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="headline"
                            class="form-control rounded-4"
                            value="{{ old('headline', $banner->headline) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Chương trình khuyến mãi</label>
                        <select name="promotion_id" class="form-select rounded-4">
                            <option value="">-- Chọn chương trình --</option>
                            @foreach($promotions as $promo)
                                <option value="{{ $promo->id }}"
                                    {{ old('promotion_id', $banner->promotion_id) == $promo->id ? 'selected' : '' }}>
                                    {{ $promo->name }} - Giảm {{ (int) ($promo->discount_percent ?? 0) }}%
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nội dung giảm giá</label>
                        <input
                            type="text"
                            name="discount_text"
                            class="form-control rounded-4"
                            value="{{ old('discount_text', $banner->discount_text) }}"
                        >
                        <small class="text-muted">Có thể để trống nếu banner đã gắn chương trình.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link khi bấm vào banner</label>
                        <input
                            type="text"
                            name="button_link"
                            class="form-control rounded-4"
                            value="{{ old('button_link', $banner->button_link) }}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả ngắn</label>
                        <textarea
                            name="description"
                            class="form-control rounded-4"
                            rows="3"
                        >{{ old('description', $banner->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Ảnh banner</label>
                        <input
                            type="file"
                            name="image"
                            class="form-control rounded-4"
                            accept="image/*"
                        >

                        @if($banner->image)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     alt="Banner"
                                     class="img-fluid rounded-4 border"
                                     style="max-width: 360px;">
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="admin-section-title mb-3">Thời gian hiển thị</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bắt đầu hiển thị</label>
                        <input
                            type="datetime-local"
                            name="start_at"
                            class="form-control rounded-4"
                            value="{{ old('start_at', optional($banner->start_at)->format('Y-m-d\TH:i')) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kết thúc hiển thị</label>
                        <input
                            type="datetime-local"
                            name="end_at"
                            class="form-control rounded-4"
                            value="{{ old('end_at', optional($banner->end_at)->format('Y-m-d\TH:i')) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Thứ tự hiển thị</label>
                        <input
                            type="number"
                            name="sort_order"
                            class="form-control rounded-4"
                            value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                        >
                    </div>

                    <div class="col-12">
                        <div class="admin-section-title mb-3">Trạng thái</div>
                    </div>

                    <div class="col-12">
                        <div class="promo-check-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    id="is_active"
                                    class="form-check-input mt-0 promo-checkbox"
                                    value="1"
                                    {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                >
                                <label for="is_active" class="mb-0 fw-semibold promo-check-label">
                                    Hiển thị banner
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button class="btn btn-admin-pink px-4 rounded-pill" type="submit">
                                <i class="fas fa-save me-2"></i>Cập nhật banner
                            </button>

                            <a href="{{ route('admin.promo-banners.index') }}" class="btn btn-light border px-4 rounded-pill">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/banner.js') }}?v={{ file_exists(public_path('js/banner.js')) ? filemtime(public_path('js/banner.js')) : time() }}"></script>
@endsection
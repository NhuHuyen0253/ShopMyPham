@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Sửa chương trình khuyến mãi</h1>
        </div>

        <a href="{{ route('admin.promotions.index') }}" class="btn btn-light border rounded-pill px-4">
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
            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-12">
                        <div class="admin-section-title mb-3">
                            <i class="fas fa-pen-to-square me-2 text-primary"></i>Thông tin chương trình
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Tên chương trình <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            class="form-control form-control-lg rounded-4"
                            value="{{ old('name', $promotion->name) }}"
                            placeholder="Ví dụ: Sale mỹ phẩm mùa hè"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Phần trăm giảm giá (%) <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            name="discount_percent"
                            class="form-control form-control-lg rounded-4"
                            value="{{ old('discount_percent', $promotion->discount_percent) }}"
                            min="1"
                            max="100"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Nội dung banner</label>
                        <textarea
                            name="description"
                            class="form-control rounded-4"
                            rows="4"
                            placeholder="Nhập nội dung mô tả ngắn gọn để hiển thị banner khuyến mãi..."
                        >{{ old('description', $promotion->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="admin-section-title mb-3">
                            <i class="fas fa-box-open me-2 text-primary"></i>Sản phẩm áp dụng
                        </div>
                    </div>

                    <div class="col-12 product-select-wrap">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap:8px;">
                            <i class="fas fa-search text-muted"></i>
                            <span>Tìm và chọn sản phẩm</span>
                        </label>

                        @php
                            $oldProductIds = array_map('intval', old('product_ids', $selectedProducts ?? []));
                        @endphp

                        <select
                            id="product_ids"
                            name="product_ids[]"
                            multiple
                            class="product-multi-select"
                            placeholder="Gõ tên sản phẩm, SKU hoặc ID..."
                        >
                            @foreach($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    {{ in_array((int) $product->id, $oldProductIds) ? 'selected' : '' }}
                                    data-sku="{{ $product->sku ?? '' }}"
                                    data-price="{{ number_format($product->price ?? 0, 0, ',', '.') }}đ"
                                >
                                    #{{ $product->id }} - {{ $product->name }}
                                    @if(!empty($product->sku))
                                        - SKU: {{ $product->sku }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="admin-section-title mb-3">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Thời gian áp dụng
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Ngày bắt đầu <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            name="start_date"
                            class="form-control rounded-4"
                            value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Ngày kết thúc <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            name="end_date"
                            class="form-control rounded-4"
                            value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <div class="promo-check-wrap">
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    id="is_active"
                                    class="promo-checkbox"
                                    value="1"
                                    {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                                >
                                <label for="is_active" class="mb-0 fw-semibold promo-check-label">
                                    Kích hoạt chương trình
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex flex-wrap pt-2" style="gap:8px;">
                            <button type="submit" class="btn btn-admin-pink px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>

                            <a href="{{ route('admin.promotions.index') }}" class="btn btn-light border px-4 rounded-pill">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/promotion-product-select.js') }}?v={{ time() }}"></script>
@endsection
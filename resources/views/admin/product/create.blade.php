@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Thêm sản phẩm mới</h1>
        </div>

        <a href="{{ route('admin.product.index') }}" class="btn-admin-light">← Quay lại danh sách</a>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-danger mb-4">
            <div class="font-semibold mb-2">Vui lòng kiểm tra lại thông tin:</div>
            <ul class="list-disc ps-4 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="productCreateForm" method="POST" action="{{ route('admin.product.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="admin-card">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thông tin cơ bản</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="admin-label">Tên sản phẩm</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="admin-input" required>
                        </div>

                        <div>
                            <label class="admin-label">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="admin-input" placeholder="Ví dụ: SP001">
                            <div class="admin-help">Mã quản lý nội bộ của sản phẩm, nên là duy nhất.</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="admin-label">Mã nhóm sản phẩm</label>
                                <input
                                    type="text"
                                    name="group_code"
                                    value="{{ old('group_code') }}"
                                    class="admin-input"
                                    placeholder="Ví dụ: sua-rua-mat-cerave"
                                >
                                <div class="admin-help">
                                    Các sản phẩm cùng loại nhưng khác dung tích phải dùng cùng một mã nhóm.
                                </div>
                            </div>

                            <div>
                                <label class="admin-label">Dung tích</label>
                                <input
                                    type="text"
                                    name="capacity"
                                    value="{{ old('capacity') }}"
                                    class="admin-input"
                                    placeholder="Ví dụ: 100ml, 250ml, 500ml"
                                >
                                <div class="admin-help">
                                    Nhập dung tích hiển thị ở trang chi tiết sản phẩm.
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="admin-label">Giá bán (VNĐ)</label>
                                <input type="number" name="price" min="0" value="{{ old('price') }}" class="admin-input" required>
                            </div>

                            <div>
                                <label class="admin-label">Giá gốc (VNĐ)</label>
                                <input type="number" name="original_price" min="0" value="{{ old('original_price') }}" class="admin-input">
                            </div>
                        </div>

                        <div class="admin-upload-box">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="is_hotdeal" name="is_hotdeal" value="1" {{ old('is_hotdeal') ? 'checked' : '' }}>
                                <span class="font-semibold text-gray-800">Gắn Hot Deal</span>
                            </label>

                            <div class="mt-4 {{ old('is_hotdeal') ? '' : 'hidden' }}" id="discountWrap">
                                <label class="admin-label">Giảm giá (%) khi Hot Deal</label>
                                <input type="number" id="discount_percent" name="discount_percent" min="0" max="100" value="{{ old('discount_percent') }}" class="admin-input">
                            </div>
                        </div>

                        <div>
                            <label class="admin-label">Danh mục</label>
                            <select name="category_id" class="admin-select" required>
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>— Chọn danh mục —</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="admin-label">Thương hiệu</label>
                            <select name="brand_id" class="admin-select" required>
                                <option value="" disabled {{ old('brand_id') ? '' : 'selected' }}>— Chọn thương hiệu —</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Mô tả và hình ảnh</h3>

                    <div class="space-y-5">
                        <div>
                            <label class="admin-label">Mô tả</label>
                            <textarea name="description" id="description" rows="6" class="admin-textarea">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="admin-label">Hướng dẫn sử dụng</label>
                            <textarea name="usage_instructions" id="usage_instructions" rows="5" class="admin-textarea">{{ old('usage_instructions') }}</textarea>
                        </div>

                        <div class="admin-upload-box">
                            <label class="admin-label">Ảnh đại diện</label>
                            <input type="file" id="image" name="image" accept="image/*" class="admin-input">
                            <div class="admin-help">JPEG/PNG/WebP, tối đa 5MB</div>
                            <div id="avatarPreview" class="mt-3"></div>
                        </div>

                        <div class="admin-upload-box">
                            <label class="admin-label">Ảnh minh hoạ</label>
                            <input type="file" name="images[]" id="images" multiple accept="image/*" class="admin-input">
                            <div class="admin-help">JPEG/PNG/WebP, tối đa 5MB/ảnh</div>

                            <div id="previewNew" class="admin-upload-preview mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div id="dropHint" class="text-xs text-gray-500 col-span-full">
                                    Kéo & thả ảnh vào đây hoặc dùng nút chọn tệp.
                                </div>
                            </div>

                            <button type="button" id="clearAllPreviews" class="btn-admin-light mt-3">Xoá tất cả ảnh vừa chọn</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-admin-pink">Tạo sản phẩm</button>
            <a href="{{ route('admin.product.index') }}" class="btn-admin-light">Huỷ</a>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="{{ asset('js/create.js') }}?v={{ file_exists(public_path('js/create.js')) ? filemtime(public_path('js/create.js')) : time() }}" defer></script>
@endsection
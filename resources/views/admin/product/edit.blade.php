@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Cập nhật sản phẩm</h1>
        </div>

        <a href="{{ route('admin.product.index') }}" class="btn-admin-light">← Quay lại danh sách</a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success mb-4">{{ session('success') }}</div>
    @endif

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

    <form method="POST" action="{{ route('admin.product.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="admin-card">
                <div class="admin-card-body">
                    <h3 class="admin-section-title">Thông tin cơ bản</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="admin-label">Tên sản phẩm</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="admin-input" required>
                        </div>

                        <div>
                            <label class="admin-label">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="admin-input">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="admin-label">Mã nhóm sản phẩm</label>
                                <input
                                    type="text"
                                    name="group_code"
                                    value="{{ old('group_code', $product->group_code) }}"
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
                                    value="{{ old('capacity', $product->capacity) }}"
                                    class="admin-input"
                                    placeholder="Ví dụ: 100ml, 250ml, 500ml"
                                >
                                <div class="admin-help">
                                    Ví dụ: 100ml, 250ml, 500ml, 1L...
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="admin-label">Giá bán (VNĐ)</label>
                                <input type="number" name="price" min="0" value="{{ old('price', $product->price) }}" class="admin-input" required>
                            </div>

                            <div>
                                <label class="admin-label">Giá gốc (VNĐ)</label>
                                <input type="number" name="original_price" min="0" value="{{ old('original_price', $product->original_price) }}" class="admin-input">
                            </div>
                        </div>

                        <div class="admin-upload-box">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_hotdeal" value="0">
                                <input type="checkbox" id="is_hotdeal" name="is_hotdeal" value="1" {{ old('is_hotdeal', $product->is_hotdeal) == 1 ? 'checked' : '' }}>
                                <span class="font-semibold text-gray-800">Gắn Hot Deal</span>
                            </label>

                            <div class="mt-4" id="discountWrap">
                                <label class="admin-label">Giảm giá (%) khi Hot Deal</label>
                                <input type="number" id="discount_percent" name="discount_percent" min="0" max="100" value="{{ old('discount_percent', $product->discount_percent) }}" class="admin-input">
                            </div>
                        </div>

                        <div>
                            <label class="admin-label">Danh mục</label>
                            <select name="category_id" class="admin-select" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="admin-label">Thương hiệu</label>
                            <select name="brand_id" class="admin-select" required>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
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
                            <textarea name="description" id="description" rows="6" class="admin-textarea">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div>
                            <label class="admin-label">Hướng dẫn sử dụng</label>
                            <textarea name="usage_instructions" id="usage_instructions" rows="5" class="admin-textarea">{{ old('usage_instructions', $product->usage_instructions) }}</textarea>
                        </div>

                        <div class="admin-upload-box">
                            <label class="admin-label">Ảnh đại diện</label>
                            <input type="file" id="image" name="image" accept="image/*" class="admin-input">

                            @if($product->image)
                                <div class="mt-4 flex items-start gap-4">
                                    <img src="{{ asset('images/product/'.$product->image) }}" class="h-24 w-24 object-cover rounded-xl border">
                                    <label class="inline-flex items-center gap-2 text-sm text-red-600">
                                        <input type="checkbox" name="remove_image" value="1">
                                        <span>Xóa ảnh đại diện hiện tại</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div class="admin-upload-box">
                            <label class="admin-label">Ảnh minh hoạ mới</label>
                            <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple class="admin-input">
                            <div id="previewNew" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4 min-h-[80px]"></div>
                        </div>

                        @if($product->images && $product->images->count())
                            <div class="admin-card border">
                                <div class="admin-card-body">
                                    <h4 class="admin-section-title mb-3">Ảnh minh hoạ hiện có</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($product->images as $img)
                                            <div class="admin-upload-box text-center" id="img-card-{{ $img->id }}">
                                                <img src="{{ asset('storage/'.$img->path.'/'.$img->file_name) }}" class="admin-thumb mb-3" alt="{{ $img->alt ?? 'Ảnh minh hoạ' }}">
                                                <button type="button"
                                                        class="admin-action-btn delete btn-delete-image"
                                                        data-action="{{ route('admin.product.images.destroy', $img) }}"
                                                        data-image-id="{{ $img->id }}"
                                                        data-product-id="{{ $product->id }}">
                                                    Xoá ảnh
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-admin-pink">Cập nhật</button>
            <a href="{{ route('admin.product.index') }}" class="btn-admin-light">Huỷ</a>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="{{ asset('js/edit.js') }}?v={{ file_exists(public_path('js/edit.js')) ? filemtime(public_path('js/edit.js')) : time() }}" defer></script>
@endsection
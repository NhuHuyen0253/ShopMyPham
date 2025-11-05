@extends('admin.layout')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Cập nhật sản phẩm</h2>

    {{-- Thông báo lỗi --}}
    @if ($errors->any())
        <div class="mb-4 border border-red-300 bg-red-50 text-red-700 p-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.product.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Tên sản phẩm</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border px-3 py-2 rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Giá</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full border px-3 py-2 rounded" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Số lượng</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" class="w-full border px-3 py-2 rounded" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border px-3 py-2 rounded" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == old('category_id', $product->category_id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Thương hiệu</label>
                    <select name="brand_id" class="w-full border px-3 py-2 rounded" required>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brand->id == old('brand_id', $product->brand_id) ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_hotdeal" value="1" class="rounded border" {{ old('is_hotdeal', $product->is_hotdeal) ? 'checked' : '' }}>
                        <span class="font-semibold">Gắn Hot Deal</span>
                    </label>
                </div>
            </div>

            <div>
                {{-- Mô tả / HDSD --}}
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Mô tả</label>
                    <textarea name="description" rows="6" class="w-full border px-3 py-2 rounded">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Hướng dẫn sử dụng</label>
                    <textarea name="usage_instructions" rows="4" class="w-full border px-3 py-2 rounded">{{ old('usage_instructions', $product->usage_instructions) }}</textarea>
                </div>

                {{-- Ảnh đại diện --}}
                <div class="mb-6">
                    <label class="block font-semibold mb-1">Ảnh đại diện</label>
                    @if ($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('images/product/' . $product->image) }}" alt="{{ $product->name }}" class="h-24 object-contain border p-1 bg-white rounded">
                        </div>
                    @endif
                    <input type="file" name="image" class="w-full border px-3 py-2 rounded">
                    <p class="text-sm text-gray-500">Chọn ảnh mới nếu muốn thay đổi (JPEG, PNG, tối đa 2MB)</p>
                </div>

                {{-- Ảnh minh hoạ (nhiều ảnh) --}}
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Ảnh minh hoạ (có thể chọn nhiều)</label>
                    <input type="file" name="images[]" multiple class="w-full border px-3 py-2 rounded" id="imagesInput" accept="image/*">
                    <p class="text-sm text-gray-500 mt-1">Bạn có thể chọn nhiều ảnh cùng lúc (JPEG, PNG, tối đa 2MB/ảnh)</p>

                    {{-- Preview ảnh mới chọn --}}
                    <div id="previewNew" class="mt-3 grid grid-cols-3 md:grid-cols-4 gap-3"></div>
                </div>

                {{-- Ảnh minh hoạ hiện có --}}
                @php
                    // $product->images có thể là collection object (id, path/url) hoặc mảng tên file
                    $existing = collect($product->images ?? []);
                @endphp

                @if($existing->count() > 0)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Ảnh minh hoạ hiện có</label>
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($existing as $img)
                            @php
                                $imgId = is_object($img) && isset($img->id) ? $img->id : null;
                                $src = is_string($img)
                                    ? asset('images/product/' . $img)
                                    : (isset($img->url) ? $img->url : (isset($img->path) ? asset($img->path) : ''));
                                $valueForDeletion = $imgId ?? $src; // gửi id nếu có, không thì gửi src
                            @endphp
                            <label class="block">
                                <img src="{{ $src }}" class="w-full aspect-square object-cover rounded border" alt="preview">
                                <div class="mt-1 flex items-center gap-2">
                                    <input type="checkbox" name="delete_images[]" value="{{ $valueForDeletion }}" class="rounded border">
                                    <span class="text-sm">Xoá ảnh này</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Tick để xoá ảnh. Nếu ảnh là từ bảng liên kết, hệ thống sẽ xoá theo <em>ID</em>; nếu là ảnh tĩnh theo tên file, sẽ xoá theo <em>đường dẫn</em>.</p>
                </div>
                @endif

            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Cập nhật</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/detail.js') }}" defer></script>
@endsection

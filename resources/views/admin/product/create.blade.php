@extends('admin.layout')

@section('content')

<div class="p-6 max-w-5xl mx-auto">
  <h2 class="text-2xl font-bold mb-4 text-gray-800">Thêm sản phẩm mới</h2>

  @if ($errors->any())
    <div class="mb-4 text-red-600">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Cột trái --}}
      <div>
        <div class="mb-4">
          <label for="name" class="block mb-1 font-medium text-gray-700">Tên sản phẩm</label>
          <input id="name" type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
        </div>

        <div class="mb-4">
          <label for="price" class="block mb-1 font-medium text-gray-700">Giá</label>
          <input id="price" type="number" name="price" min="0" class="w-full border rounded px-3 py-2" value="{{ old('price') }}" required>
        </div>

        <div class="mb-4">
          <label for="quantity" class="block mb-1 font-medium text-gray-700">Số lượng</label>
          <input id="quantity" type="number" name="quantity" min="0" class="w-full border rounded px-3 py-2" value="{{ old('quantity', 0) }}" required>
        </div>

        <div class="mb-4">
          <label for="category_id" class="block mb-1 font-medium text-gray-700">Danh mục</label>
          {{-- FIX: thêm id để label for hoạt động --}}
          <select id="category_id" name="category_id" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Chọn danh mục --</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-4">
          <label for="brand_id" class="block mb-1 font-medium text-gray-700">Thương hiệu</label>
          {{-- FIX: thêm id để label for hoạt động --}}
          <select id="brand_id" name="brand_id" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Chọn thương hiệu --</option>
            @foreach($brands as $brand)
              <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                {{ $brand->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-4">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_hotdeal" value="1" class="rounded border" {{ old('is_hotdeal') ? 'checked' : '' }}>
            <span class="font-semibold">Gắn Hot Deal</span>
          </label>
        </div>
      </div>

      {{-- Cột phải --}}
      <div>
        <div class="mb-4">
          <label for="description" class="block mb-1 font-medium text-gray-700">Mô tả</label>
          <textarea id="description" name="description" rows="6" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
          <label for="usage_instructions" class="block mb-1 font-medium text-gray-700">Hướng dẫn sử dụng</label>
          <textarea id="usage_instructions" name="usage_instructions" rows="4" class="w-full border rounded px-3 py-2">{{ old('usage_instructions') }}</textarea>
        </div>

        {{-- Ảnh đại diện --}}
        <div class="mb-6">
          <label for="image" class="block mb-1 font-medium text-gray-700">Ảnh đại diện</label>
          <input id="image" type="file" name="image" class="w-full border rounded px-3 py-2" accept="image/*">
          <p class="text-sm text-gray-500">Định dạng: JPEG/PNG/WebP, tối đa 4MB</p>
        </div>

        {{-- Ảnh minh hoạ (nhiều ảnh) --}}
        <div class="mb-2">
          <label for="imagesInput" class="block mb-2 font-medium text-gray-700">Ảnh minh hoạ (có thể chọn nhiều)</label>
          <input id="imagesInput" type="file" name="images[]" multiple class="w-full border rounded px-3 py-2" accept="image/*">
          <p class="text-sm text-gray-500 mt-1">Định dạng: JPEG/PNG/WebP, tối đa 4MB/ảnh</p>
        </div>

        {{-- Preview + ALT & thứ tự --}}
        <div id="previewNew" class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-4"></div>
        <div id="hiddenMetaWrap"></div>
      </div>
    </div>

    <div class="pt-4 flex justify-end">
      <a href="{{ route('admin.product.index') }}"
         class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Hủy</a>
      <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">
        Lưu
      </button>
    </div>
  </form>
</div>
@endsection

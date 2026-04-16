@extends('admin.layout') 

@section('content')
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Chỉnh sửa thương hiệu</h2>
    <form action="{{ route('admin.brand.update', $brand) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf 
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tên thương hiệu</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" value="{{ old('name', $brand->name) }}" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Ảnh thương hiệu</label>
            <input type="file" name="image" accept="image/*" class="w-full">

            @if ($brand->image)
                <img src="{{ asset('images/brand/' . $brand->image) }}"
                                         alt="{{ $brand->name }}"
                                         class="h-16 w-24 object-contain rounded-lg bg-white border p-1">
            @endif
        </div>

        <button type="submit" class="bg-pink-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Cập nhật</button>
    
    </form>
    <a href="{{ route('admin.brand.index') }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-xl border text-gray-600 hover:bg-gray-50 text-sm">
            ← Quay lại danh sách
    </a>
</div>
@endsection

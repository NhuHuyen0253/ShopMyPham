@extends('admin.layout') 

@section('content')
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Thêm thương hiệu</h2>
    <form action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tên thương hiệu</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Ảnh thương hiệu (tùy chọn)</label>
            <input type="file" name="image" accept="image/*" class="w-full">
        </div>
        <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Lưu</button>
    </form>
</div>
@endsection

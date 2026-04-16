@extends('admin.layout')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Sửa danh mục</h2>

    <div class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <form action="{{ route('admin.category.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium mb-2">Tên danh mục</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Loại</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">-- Chọn loại --</option>
                    <option value="face" {{ $category->type == 'face' ? 'selected' : '' }}>Face</option>
                    <option value="hair" {{ $category->type == 'hair' ? 'selected' : '' }}>Hair</option>
                    <option value="body" {{ $category->type == 'body' ? 'selected' : '' }}>Body</option>
                    <option value="makeup" {{ $category->type == 'makeup' ? 'selected' : '' }}>Makeup</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-pink-500 hover:bg-pink-600 text-white px-5 py-2 rounded-lg">
                    Cập nhật
                </button>
                <a href="{{ route('admin.category.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
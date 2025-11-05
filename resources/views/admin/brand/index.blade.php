@extends('admin.layout') 

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Danh sách thương hiệu</h2>
        <a href="{{ route('admin.brand.create') }}" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded text-sm">+ Thêm thương hiệu</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Ảnh</th>
                <th class="px-4 py-2 text-left">Tên thương hiệu</th>
                <th class="px-4 py-2 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($brands as $brand)
            <tr class="border-t">
                <td class="px-4 py-2">
                    @if($brand->image)
                        <img src="{{ asset( 'images/brand/' .$brand->image) }}" alt="{{ $brand->name }}" class="h-10 w-20 object-contain">
                    @else
                        <span class="text-gray-400 italic">Chưa có ảnh</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $brand->name }}</td>
                <td class="px-4 py-2 text-right space-x-2">
                    <a href="{{ route('admin.brand.edit', $brand) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form action="{{ route('admin.brand.destroy', $brand) }}" method="POST" class="inline-block" onsubmit="return confirm('Xác nhận xoá?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $brands->links() }}
    </div>
</div>
@endsection

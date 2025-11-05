@extends('admin.layout')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.product.create') }}" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded text-sm">+ Thêm sản phẩm</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Ảnh</th>
                <th class="px-4 py-2 text-left">Tên</th>
                <th class="px-4 py-2 text-left">Giá</th>
                <th class="px-4 py-2 text-left">SL</th>
                <th class="px-4 py-2 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-t">
                    <td class="px-4 py-2">
                        @if($product->image)
                            <img src="{{ asset('images/product/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-20 object-contain">
                        @else
                            <span class="text-gray-400 italic">Chưa có ảnh</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ number_format($product->price) }}₫</td>
                    <td class="px-4 py-2">{{ $product->quantity }}</td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('admin.product.edit', $product) }}" class="text-blue-600 hover:underline">Sửa</a>
                        <form action="{{ route('admin.product.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn xoá?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection

@extends('layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Sản phẩm của thương hiệu {{ $brands->name }}</h2>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset('images/product/' . $product->image) }}" 
                             class="card-img-top" 
                             alt="{{ $product->name }}">
                    </a>
                    <div class="card-body text-center">
                        <h6>{{ $product->name }}</h6>
                        <p class="text-danger">{{ number_format($product->price) }} đ</p>
                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @empty
            <p>Chưa có sản phẩm nào cho thương hiệu này.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
</div>
    <p><a href="{{ route('brands') }}" class="btn btn-secondary">← Quay lại danh sách thương hiệu</a></p>
@endsection

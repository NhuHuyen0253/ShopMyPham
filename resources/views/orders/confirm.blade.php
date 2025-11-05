@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Xác nhận đơn hàng</h2>

    @if ($product)
        <div class="card p-4 shadow-sm">
            <h4 class="mb-3">{{ $product->name }}</h4>
            <p><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
            <p><strong>Số lượng:</strong> {{ $quantity }}</p>
            <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($total, 0, ',', '.') }} đ</span></p>

            <form method="POST" action="{{ route('order.checkout') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="{{ $quantity }}">
                <button type="submit" class="btn btn-success">Xác nhận mua hàng</button>
                <a href="{{ url('/') }}" class="btn btn-secondary">Quay lại</a>
            </form>

        </div>
    @endif
</div>
@endsection

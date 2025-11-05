@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Thông tin mua hàng</h2>

    @if ($product)
        <div class="card p-4 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('images/product/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded" style="max-height: 200px;">
                </div>

                <div class="col-md-8">
                    <h4>{{ $product->name }}</h4>
                    <p><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
                    <p><strong>Số lượng:</strong> {{ $quantity }}</p>
                    <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($total, 0, ',', '.') }} đ</span></p>

                    <form method="POST" action="{{ route('order.confirm') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="{{ $quantity }}">
                        <button type="submit" class="btn btn-primary">Tiếp tục xác nhận</button>
                        <a href="{{ url('/') }}" class="btn btn-secondary">Hủy</a>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
@endsection

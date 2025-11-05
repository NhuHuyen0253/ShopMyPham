@extends('layout')

@section('content')
<div class="container my-4">
    <div class="row">
        {{-- Sidebar bên trái (1/4) --}}
        <div class="col-md-3">
            <h5 class="mb-3">CHĂM SÓC BODY</h5>
            <ul class="list-group">
                @foreach ($categories as $category)
                    <li class="list-group-item">
                        <a href="{{ route('body.category', $category->slug) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-9">
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('images/product/' . $product->image) }}"
                                 alt="{{ $product->name }}" class="card-img-top">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text flex-grow-1">{{ $product->short_description }}</p>
                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary mt-auto">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection

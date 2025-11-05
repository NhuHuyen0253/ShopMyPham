@extends('layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Xem {{ $grouped->flatten()->count() }} thương hiệu</h2>

    <div class="d-flex flex-wrap mb-3">
        @foreach(range('A', 'Z') as $char)
            <a href="#{{ $char }}" class="btn btn-sm btn-outline-secondary m-1">{{ $char }}</a>
        @endforeach
        <a href="#0-9" class="btn btn-sm btn-outline-secondary m-1">0-9</a>
    </div>

    @foreach($grouped as $letter => $brands)
        <h4 id="{{ $letter }}" class="mt-4">{{ $letter }}</h4>
            <div class="row">
                @foreach($brands as $brand)
                    <div class="col-6 col-md-2 text-center mb-4">
                        <div class="card h-100 p-2 brand-cart">
                            <a href="{{ route('brands.show', $brand->slug) }}">
                                <img src="{{ asset('images/brand/' . $brand->image) }}"
                                    alt="{{ $brand->name }}"
                                    class="brand-logo" />
                                <div>{{ $brand->name }}</div>
                            </a>
                        </div>
                    </div>
                @endforeach
        </div>
    @endforeach
</div>
@endsection

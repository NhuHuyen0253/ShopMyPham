@extends('layout')

@section('content')
    <div class="container">
        <h1 class="mb-4">Sản phẩm mới nhất</h1>

        <!-- Hiển thị danh sách sản phẩm -->
        @include('product_list', ['products' => $products])

    </div>
@endsection

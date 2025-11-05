@extends('layout')

@section('title', 'Hot Deal - Khuyến mãi sốc')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style') }}">

<!-- Nút nổi Hot Deal -->
<div class="fixed top-4 left-4 z-50">
    <img src="{{ asset('images/home/hot_deal.jpg') }}" 
         alt="Hot Deal" 
         class="img-hotdeal">
</div>

<div class="hotdeal-container pt-24"> <!-- thêm padding top để không bị che -->
    <h2 class="hotdeal-title text-center text-2xl font-bold text-red-500 mb-8">🔥 Ưu đãi HOT trong ngày</h2>
</div>
@endsection

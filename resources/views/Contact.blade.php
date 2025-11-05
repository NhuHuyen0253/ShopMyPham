@extends('layout')

@section('title', 'Trang Hỗ Trợ')

@section('content')
<div class="support-container">
    <h1> Hỗ Trợ Khách Hàng</h1>
    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ chúng tôi qua các phương thức sau:</p>

    <ul>
        <li>Email: xinhxinhshop@gmail.com</li>
        <li>Hotline: 038 4528 393</li>
        <li>Fanpage: xinhxinhshop.com/page</li>
    </ul>   
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/support.css') }}">
@endpush

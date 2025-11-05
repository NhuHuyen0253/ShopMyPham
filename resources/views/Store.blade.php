@extends('layout')

@section('title', 'Thông tin cửa hàng')

@section('content')
    <h1 class="text-center mb-4">Thông tin cửa hàng</h1>

    <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận 1, TP.HCM</p>
    <p><strong>Điện thoại:</strong> 0909 123 456</p>
    <p><strong>Giờ mở cửa:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>

    <h3 class="mt-5 mb-3">Bản đồ Google Maps</h3>
    <div id="map" style="height: 500px; width: 700px;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1320.3032177353014!2d105.74534997224828!3d10.035508251316283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a08867ea491299%3A0x31a5b8250a2f34ef!2zMjg2IDkxQiwgTG9uZyBUdXnhu4FuLCBCw6xuaCBUaOG7p3ksIEPhuqduIFRoxqEsIFZp4buHdCBOYW0!5e1!3m2!1svi!2s!4v1748423597611!5m2!1svi!2s" 
     style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    
@endsection

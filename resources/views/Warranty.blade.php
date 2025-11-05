@extends('layouts.app')

@section('title', 'Chính sách bảo hành')

@section('content')
<link rel="stylesheet" href="{{ asset('css/warranty.css') }}">

<div class="warranty-container">
    <h1 class="warranty-title">Chính sách Bảo Hành</h1>
    <p>Chúng tôi cam kết cung cấp sản phẩm chất lượng cao đến khách hàng. Tất cả sản phẩm được bảo hành theo chính sách sau:</p>
    <ul class="warranty-list">
        <li>Bảo hành 1 đổi 1 trong vòng 7 ngày nếu sản phẩm lỗi từ nhà sản xuất.</li>
        <li>Không áp dụng bảo hành cho sản phẩm bị hư hại do người dùng.</li>
        <li>Quý khách vui lòng giữ lại hóa đơn và bao bì sản phẩm khi bảo hành.</li>
    </ul>

    <form method="POST" action="{{ route('warranty') }}" class="warranty-form">
        @csrf

        <div class="form-group">
            <label>Số điện thoại mua hàng <span class="required">*</span></label>
            <input type="text" name="phone" required placeholder="Nhập số điện thoại">
        </div>

        <div class="form-group">
            <label>Sản phẩm cần kích hoạt bảo hành <span class="required">*</span></label>
            <select name="product" required>
                <option value="">Vui lòng chọn sản phẩm</option>
                <option value="Máy rửa mặt">Máy rửa mặt</option>
                <option value="Máy massage mặt">Máy massage mặt</option>
            </select>
        </div>

        <div class="form-group">
            <label>Số Serial / Mã đơn hàng <span class="required">*</span></label>
            <input type="text" name="serial_numbers" required placeholder="Nhập serial, cách nhau dấu phẩy">
        </div>

        <div class="form-group">
            <label>Ghi chú</label>
            <textarea name="note" rows="3" placeholder="Vui lòng mô tả nội dung ngắn gọn"></textarea>
        </div>

        <button type="submit" class="submit-btn">Gửi yêu cầu</button>
    </form>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert error">
            @foreach($errors->all() as $error)
                <p>- {{ $error }}</p>
            @endforeach
        </div>
    @endif
</div>
@endsection

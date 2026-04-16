@extends('layout')

@section('title', 'Hướng dẫn mua hàng')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Hướng Dẫn Mua Hàng</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop mang đến hình thức mua sắm linh hoạt tại cửa hàng và trên website,<br>
                            giúp khách hàng dễ dàng lựa chọn sản phẩm và đặt hàng nhanh chóng.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Mua tại cửa hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Địa chỉ cửa hàng: <strong>284B/91B Long Tuyền</strong>.</li>
                            <li class="mb-2">- Thời gian hoạt động: <strong>8:00 - 22:00</strong> mỗi ngày.</li>
                            <li>- Khách hàng có thể lựa chọn sản phẩm trực tiếp và thanh toán tại quầy.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Mua hàng online</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Truy cập website chính thức của <strong>XinhXinhShop</strong>.</li>
                            <li class="mb-2">- Tìm kiếm và lựa chọn sản phẩm phù hợp với nhu cầu.</li>
                            <li class="mb-2">- Thêm sản phẩm vào giỏ hàng hoặc chọn mua ngay.</li>
                            <li class="mb-2">- Điền thông tin nhận hàng đầy đủ.</li>
                            <li>- Chọn phương thức thanh toán và xác nhận đơn hàng.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">3. Sau khi đặt hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khách hàng có thể theo dõi trạng thái đơn hàng trực tiếp trên website.</li>
                            <li>- Đối với đơn đã bàn giao cho đơn vị vận chuyển, khách hàng có thể kiểm tra vận đơn để biết thời gian giao hàng dự kiến.</li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('contact.contact') }}" class="btn btn-secondary">
                    ← Quay lại 
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
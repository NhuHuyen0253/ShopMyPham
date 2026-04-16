@extends('layout')

@section('title', 'Chính sách thanh toán')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Chính Sách Thanh Toán</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop cung cấp nhiều phương thức thanh toán an toàn, nhanh chóng
                            và tiện lợi <br> nhằm mang đến trải nghiệm mua sắm dễ dàng cho khách hàng.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Phương thức thanh toán</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khi mua trực tiếp tại cửa hàng: thanh toán bằng tiền mặt hoặc chuyển khoản.</li>
                            <li>- Khi mua online: khách hàng có thể chọn thanh toán khi nhận hàng (COD) hoặc thanh toán trực tuyến qua VNPAY.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Quy trình thanh toán</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">Bước 1: Chọn sản phẩm cần mua.</li>
                            <li class="mb-2">Bước 2: Nhập đầy đủ thông tin nhận hàng.</li>
                            <li class="mb-2">Bước 3: Lựa chọn phương thức thanh toán phù hợp.</li>
                            <li>Bước 4: Xác nhận đơn hàng và hoàn tất thanh toán.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">3. Chính sách hoàn tiền</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khách hàng được hỗ trợ hoàn tiền nếu hủy đơn trước khi shop tiến hành gửi hàng.</li>
                            <li class="mb-2">- Trường hợp phát sinh lỗi giao dịch hoặc thanh toán không thành công, shop sẽ kiểm tra và hỗ trợ xử lý phù hợp.</li>
                            <li>- Thời gian hoàn tiền dự kiến từ <strong>3 - 7 ngày làm việc</strong>, tùy theo phương thức thanh toán và ngân hàng liên kết.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 4 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">4. Lưu ý khi thanh toán</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khách hàng nên kiểm tra kỹ thông tin đơn hàng trước khi xác nhận thanh toán.</li>
                            <li>- Lưu lại biên nhận hoặc thông tin chuyển khoản để thuận tiện cho việc đối soát khi cần thiết.</li>
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
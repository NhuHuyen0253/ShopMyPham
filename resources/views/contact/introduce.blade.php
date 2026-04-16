@extends('layout')

@section('title', 'Giới thiệu')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">XinhXinhShop <br> Đẹp hơn mỗi ngày, tự tin mỗi phút</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop là cửa hàng mỹ phẩm dành cho những khách hàng yêu thích làm đẹp.
                            Chúng tôi luôn mong muốn mang đến trải nghiệm mua sắm tiện lợi, thoải mái và đáng tin cậy cho mọi khách hàng.
                        </p>
                    </div>

                    {{-- Logo --}}
                    <div class="text-center mb-4 mb-md-5">
                        <img src="{{ asset('images/home/xinhxinhshop.png') }}"
                             alt="Logo XinhXinhShop"
                             class="img-fluid  shadow-sm"
                             style="width: 140px; height: 140px; object-fit: cover;">
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Giới thiệu</h4>
                        <p class="text-muted mb-0" style="line-height: 1.8;">
                            <strong>XinhXinhShop</strong> cung cấp các sản phẩm chăm sóc da, trang điểm và chăm sóc cá nhân
                            phù hợp với nhu cầu hằng ngày. Chúng tôi luôn chú trọng đến chất lượng phục vụ,
                            sự hài lòng của khách hàng và trải nghiệm mua sắm tiện lợi trên cả cửa hàng trực tiếp lẫn website.
                        </p>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Thông tin cửa hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2"><strong>- Giờ mở cửa:</strong> 8:00 - 22:00 mỗi ngày</li>
                            <li class="mb-2"><strong>- Địa chỉ:</strong> 284B/91B Phường Long Tuyền, TP Cần Thơ</li>
                            <li class="mb-2"><strong>- Số điện thoại:</strong> 0384528393</li>
                            <li><strong>- Email:</strong> CSKHXinhXinhShop@gmail.com</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">3. Cam kết</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Tư vấn tận tình, phù hợp với nhu cầu của từng khách hàng.</li>
                            <li class="mb-2">- Phục vụ chu đáo, thân thiện và chuyên nghiệp.</li>
                            <li class="mb-2">- Hỗ trợ nhanh chóng trong quá trình mua hàng và sau bán hàng.</li>
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
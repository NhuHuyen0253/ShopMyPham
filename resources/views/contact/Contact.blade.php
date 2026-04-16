@extends('layout')

@section('title', 'Liên hệ')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            {{-- Tiêu đề --}}
            <div class="text-center mb-4 mb-md-5">
                <h1 class="fw-bold text-dark mb-2">Liên Hệ & Hỗ Trợ</h1>
            </div>

            <div class="row g-4">
                {{-- Box 1 --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3">Chăm Sóc Khách Hàng</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="{{ url('/contact/return-policy') }}" class="text-decoration-none text-blue">
                                        Chính sách đổi trả
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ url('/contact/privacy-policy') }}" class="text-decoration-none text-blue">
                                        Chính sách bảo mật
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ url('/contact/payment-policy') }}" class="text-decoration-none text-blue">
                                        Chính sách thanh toán
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ url('/contact/purchase-instructions') }}" class="text-decoration-none text-blue">
                                        Hướng dẫn mua hàng
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/contact/ship') }}" class="text-decoration-none text-blue">
                                        Vận chuyển
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Box 2 --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3">Thông Tin Cửa Hàng</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="{{ url('/contact/introduce') }}" class="text-decoration-none text-blue">
                                        Giới thiệu
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/contact/recruitment') }}" class="text-decoration-none text-blue">
                                        Tuyển dụng
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Box 3 --}}
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3">Góp Ý - Khiếu Nại</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <strong>Số điện thoại:</strong><br>
                                    <span class="text-muted">0384528393</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Email:</strong><br>
                                    <span class="text-muted">CSKHXinhXinhShop@gmail.com</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Thời gian hỗ trợ:</strong><br>
                                    <span class="text-muted">8:00 - 22:00 mỗi ngày</span>
                                </li>
                                <li>
                                    <strong>Địa chỉ:</strong><br>
                                    <span class="text-muted">284B/91B Phường Long Tuyền, Thành Phố Cần Thơ</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
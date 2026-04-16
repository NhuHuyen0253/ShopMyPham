@extends('layout')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                   
                    <ul class="list-group list-group-flush account-side-menu">
                        <li class="list-group-item {{ request()->routeIs('profile.info') ? 'active' : '' }}">
                            <a href="{{ route('profile.info') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-user"></i>
                                <span>Thông tin tài khoản</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-box"></i>
                                <span>Đơn hàng của tôi</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-heart"></i>
                                <span>Danh sách yêu thích</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.rebuy') ? 'active' : '' }}">
                            <a href="{{ route('profile.rebuy') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Mua lại</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.faq') ? 'active' : '' }}">
                            <a href="{{ route('profile.faq') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-circle-question"></i>
                                <span>Hỏi đáp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-4 p-md-5 border-bottom bg-white">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                        <img
                            src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/avatar-placeholder.jpg') }}"
                            alt="Avatar"
                            class="rounded-circle border shadow-sm"
                            style="width: 110px; height: 110px; object-fit: cover;"
                        >

                        <div class="flex-grow-1 text-center text-md-start">
                            <p class="text-muted mb-1">Xin chào,</p>
                            <h3 class="fw-bold mb-2 text-dark">{{ auth()->user()->name }}</h3>
                            <p class="mb-0 text-muted">{{ auth()->user()->email ?? 'Chưa cập nhật email' }}</p>
                        </div>

                        <div class="text-center text-md-end">
                            <a href="{{ route('profile.edit') }}" class="btn btn-pink px-4 rounded-pill fw-semibold">
                                Chỉnh sửa hồ sơ
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 p-md-5 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="mb-0 fw-bold text-dark">Thông tin tài khoản</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Họ và tên</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->name ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Số điện thoại</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->phone ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Email</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->email ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Ngày sinh</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->dob ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Giới tính</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->gender ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="text-muted small mb-1">Địa chỉ nhận hàng</div>
                                <div class="fw-semibold text-dark">
                                    {{ auth()->user()->address ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
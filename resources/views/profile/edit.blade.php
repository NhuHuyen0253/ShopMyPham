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
                            <p class="text-muted mb-1">Chỉnh sửa hồ sơ của bạn</p>
                            <h3 class="fw-bold mb-2 text-dark">{{ auth()->user()->name }}</h3>
                            <p class="mb-0 text-muted">{{ auth()->user()->email ?? 'Chưa cập nhật email' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 p-md-5 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="mb-0 fw-bold text-dark">Cập nhật thông tin tài khoản</h4>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success rounded-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger rounded-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM cập nhật thông tin --}}
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-semibold">Tên người dùng</label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control rounded-4">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control rounded-4">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control rounded-4">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="date_of_birth" class="form-label fw-semibold">Ngày tháng năm sinh</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}" class="form-control rounded-4">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label fw-semibold">Giới tính</label>
                                <select id="gender" name="gender" class="form-select rounded-4">
                                    <option value="Nam"  {{ old('gender', auth()->user()->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ"   {{ old('gender', auth()->user()->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    <option value="Khác" {{ old('gender', auth()->user()->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="avatar" class="form-label fw-semibold">Ảnh đại diện</label>
                                <input type="file" id="avatar" name="avatar" class="form-control rounded-4">
                                <div class="form-text">Chọn ảnh mới nếu bạn muốn thay đổi avatar.</div>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Địa chỉ nhận hàng</label>
                                <input type="text" id="address" name="address" value="{{ old('address', auth()->user()->address) }}" class="form-control rounded-4">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-pink px-4 py-2 rounded-pill fw-semibold">
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>

                    <hr class="my-5">

                    {{-- Đổi mật khẩu --}}
                    <div class="mb-4">
                        <h4 class="mb-1 fw-bold text-dark">Đổi mật khẩu</h4>
                        <p class="text-muted mb-0">Cập nhật mật khẩu mới để bảo mật tài khoản tốt hơn.</p>
                    </div>

                    @if(session('password_success'))
                        <div class="alert alert-success rounded-4">{{ session('password_success') }}</div>
                    @endif

                    @if(session('password_error'))
                        <div class="alert alert-danger rounded-4">{{ session('password_error') }}</div>
                    @endif

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control rounded-4" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control rounded-4" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                                <input type="password" name="new_password_confirmation" class="form-control rounded-4" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold">
                                Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
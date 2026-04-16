@extends('admin.layout')

@section('title', 'Đổi mật khẩu admin')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Đổi mật khẩu</h1>
            <p class="admin-page-subtitle mb-0">
                Cập nhật mật khẩu mới để tăng cường bảo mật cho tài khoản quản trị.
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="admin-card">
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="form-control admin-input" required>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control admin-input" required>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" class="form-control admin-input" required>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-admin-pink rounded-pill px-4">
                                <i class="fas fa-save me-2"></i>Cập nhật mật khẩu
                            </button>

                            <a href="{{ route('admin.profile.show') }}" class="btn btn-light border rounded-pill px-4">
                                Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
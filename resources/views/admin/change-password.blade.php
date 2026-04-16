@extends('admin.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-6 col-xl-5">
            <div class="password-card bg-white shadow-sm rounded-4 overflow-hidden border-0">
                
                <div class="password-card-header text-center text-white py-4 px-4">
                    <div class="password-icon mx-auto mb-3">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Thay đổi mật khẩu</h2>
                </div>

                <div class="p-4 p-md-5">
                    @if(session('success'))
                        <div class="alert custom-alert-success d-flex align-items-start gap-3 rounded-4 border-0 mb-4">
                            <div class="alert-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Thành công</div>
                                <div>{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert custom-alert-danger d-flex align-items-start gap-3 rounded-4 border-0 mb-4">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Có lỗi xảy ra</div>
                                <div>{{ session('error') }}</div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.password.update') }}" method="POST" class="password-form">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Mật khẩu hiện tại</label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                    class="form-control custom-input @error('current_password') is-invalid @enderror"
                                    placeholder="Nhập mật khẩu hiện tại"
                                >
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Mật khẩu mới</label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                    class="form-control custom-input @error('new_password') is-invalid @enderror"
                                    placeholder="Nhập mật khẩu mới"
                                >
                            </div>
                            @error('new_password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Xác nhận mật khẩu mới</label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <input
                                    type="password"
                                    name="new_password_confirmation"
                                    required
                                    class="form-control custom-input"
                                    placeholder="Nhập lại mật khẩu mới"
                                >
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn password-submit-btn rounded-3 py-3 fw-semibold">
                                <i class="fas fa-save me-2"></i>Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('admin.layout')

@section('title', 'Thông tin tài khoản admin')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Thông tin tài khoản</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="admin-card h-100">
                <div class="card-body p-4 text-center">
                    <div class="profile-avatar mx-auto mb-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $admin->name ?? 'Admin' }}</h4>
                    <div class="text-muted mb-2">{{ $admin->email ?? 'Chưa có email' }}</div>
                    <div class="text-muted">{{ $admin->phone ?? 'Chưa có số điện thoại' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Chi tiết tài khoản</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Họ tên</label>
                            <div class="profile-info-box">{{ $admin->name ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <div class="profile-info-box">{{ $admin->email ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">Số điện thoại</label>
                            <div class="profile-info-box">{{ $admin->phone ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">Ngày tạo tài khoản</label>
                            <div class="profile-info-box">
                                {{ $admin->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">Ngày cập nhật tài khoản</label>
                            <div class="profile-info-box">
                                {{ $admin->updated_at?->format('d/m/Y H:i') ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.profile.password.edit') }}" class="btn btn-admin-pink rounded-pill px-4">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
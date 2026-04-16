@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-employee-form-page">

    <div class="admin-page-header mb-4">
        <div>
            <a href="{{ route('admin.employee.index') }}" class="admin-back-link admin-badge admin-badge-gray">
                ← Quay lại danh sách
            </a>
            <h1 class="admin-page-title mt-2 mb-0">Thêm nhân viên mới</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-danger mb-4">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mt-2 mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="admin-label">Họ và tên</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="admin-input"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="dob" class="admin-label">Ngày tháng năm sinh</label>
                        <input
                            type="date"
                            id="dob"
                            name="dob"
                            class="admin-input"
                            value="{{ old('dob') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="gender" class="admin-label">Giới tính</label>
                        <select id="gender" name="gender" class="admin-select" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Nam</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Nữ</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone" class="admin-label">Số điện thoại</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="admin-input"
                            value="{{ old('phone') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="email" class="admin-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="admin-input"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="position" class="admin-label">Vị trí công việc</label>
                        <input
                            type="text"
                            id="position"
                            name="position"
                            class="admin-input"
                            value="{{ old('position') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="hire_date" class="admin-label">Ngày vào làm</label>
                        <input
                            type="date"
                            id="hire_date"
                            name="hire_date"
                            class="admin-input"
                            value="{{ old('hire_date') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="status" class="admin-label">Trạng thái</label>
                        <select id="status" name="status" class="admin-select" required>
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Còn làm việc</option>
                            <option value="Resigned" {{ old('status') == 'Resigned' ? 'selected' : '' }}>Đã nghỉ</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="avatar" class="admin-label">Ảnh đại diện</label>
                    <div class="admin-upload-box">
                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            class="admin-input"
                            accept="image/*"
                        >
                        <div class="admin-help">Chọn ảnh đại diện cho nhân viên. Có thể bỏ trống nếu chưa cần.</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-admin-pink">
                        Lưu nhân viên
                    </button>

                    <a href="{{ route('admin.employee.index') }}" class="btn-admin-light">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
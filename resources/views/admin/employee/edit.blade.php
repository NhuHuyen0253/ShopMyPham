@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-employee-form-page">

    <div class="admin-page-header mb-4">
        <div>
            <a href="{{ route('admin.employee.index') }}" class="admin-back-link admin-badge admin-badge-gray">
                ← Quay lại danh sách
            </a>
            <h1 class="admin-page-title mt-2 mb-0">Cập nhật thông tin nhân viên</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-danger mb-4">
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul class="mt-2 mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label" for="name">Họ tên</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="admin-input"
                            value="{{ old('name', $employee->name) }}"
                            required
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="dob">Ngày sinh</label>
                        <input
                            type="date"
                            id="dob"
                            name="dob"
                            class="admin-input"
                            value="{{ old('dob', $employee->dob ? \Illuminate\Support\Carbon::parse($employee->dob)->format('Y-m-d') : '') }}"
                            required
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="gender">Giới tính</label>
                        @php $g = old('gender', $employee->gender); @endphp
                        <select id="gender" name="gender" class="admin-select" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="Female" {{ $g === 'Female' ? 'selected' : '' }}>Nữ</option>
                            <option value="Male" {{ $g === 'Male' ? 'selected' : '' }}>Nam</option>
                            <option value="Other" {{ $g === 'Other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="admin-label" for="phone">Số điện thoại</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="admin-input"
                            value="{{ old('phone', $employee->phone) }}"
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="admin-input"
                            value="{{ old('email', $employee->email) }}"
                            required
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="position">Chức vụ</label>
                        <input
                            type="text"
                            id="position"
                            name="position"
                            class="admin-input"
                            value="{{ old('position', $employee->position) }}"
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="hire_date">Ngày vào làm</label>
                        <input
                            type="date"
                            id="hire_date"
                            name="hire_date"
                            class="admin-input"
                            value="{{ old('hire_date', $employee->hire_date ? \Illuminate\Support\Carbon::parse($employee->hire_date)->format('Y-m-d') : '') }}"
                            required
                        >
                    </div>

                    <div>
                        <label class="admin-label" for="status">Trạng thái</label>
                        @php $s = old('status', $employee->status); @endphp
                        <select id="status" name="status" class="admin-select" required>
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="Active" {{ $s === 'Active' ? 'selected' : '' }}>Còn làm việc</option>
                            <option value="Resigned" {{ $s === 'Resigned' ? 'selected' : '' }}>Đã nghỉ</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="admin-label">Ảnh đại diện hiện tại</label>
                    <div class="admin-current-avatar-box">
                        @if($employee->avatar)
                            <img
                                src="{{ asset('storage/' . $employee->avatar) }}"
                                alt="Avatar {{ $employee->name }}"
                                class="admin-current-avatar"
                            >
                        @else
                            <div class="admin-current-avatar admin-current-avatar-empty">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <label class="admin-label" for="avatar">Tải ảnh mới</label>
                    <div class="admin-upload-box">
                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            class="admin-input"
                            accept=".jpg,.jpeg,.png,.webp"
                        >
                        <div class="admin-help">
                            Tối đa 2MB. Định dạng hỗ trợ: jpg, jpeg, png, webp.
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('admin.employee.index') }}" class="btn-admin-light">
                        Quay lại
                    </a>
                    <button type="submit" class="btn-admin-pink">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
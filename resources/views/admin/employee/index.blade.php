@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-employee-page">

    {{-- Header --}}
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Danh sách nhân viên</h1>
        </div>

        <a href="{{ route('admin.employee.create') }}" class="btn-admin-pink admin-header-btn">
            <i class="fas fa-plus-circle"></i>
            <span>Thêm nhân viên mới</span>
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">
            <strong>Thành công!</strong> {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.employee.index') }}">
                <div class="admin-search-wrap">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Nhập họ tên, email hoặc số điện thoại..."
                        class="admin-search-input"
                    />
                    <button type="submit" class="btn-admin-pink admin-search-btn">
                        Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrap">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">Danh sách nhân viên</div>
            <div class="admin-table-count">
                Tổng:
                <strong>
                    {{ method_exists($employees, 'total') ? number_format($employees->total()) : count($employees) }}
                </strong>
                nhân viên
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vị trí</th>
                        <th>Ngày vào làm</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td style="width: 76px;">
                                @if($employee->avatar)
                                    <img
                                        src="{{ asset('storage/' . $employee->avatar) }}"
                                        alt="Avatar {{ $employee->name }}"
                                        class="admin-employee-avatar"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="admin-employee-avatar admin-employee-avatar-fallback">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="admin-employee-name">{{ $employee->name }}</div>
                            </td>

                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone ?? '—' }}</td>
                            <td>{{ $employee->position ?? '—' }}</td>
                            <td>
                                {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : '—' }}
                            </td>

                            <td>
                                @if($employee->status === 'Active')
                                    <span class="admin-badge admin-badge-green">Còn làm việc</span>
                                @else
                                    <span class="admin-badge admin-badge-red">Đã nghỉ</span>
                                @endif
                            </td>

                            <td>
                                <div class="admin-action-group">
                                    <a
                                        href="{{ route('admin.employee.edit', $employee->id) }}"
                                        class="admin-action-btn edit"
                                        title="Sửa thông tin"
                                    >
                                        <i class="fas fa-edit"></i>
                                        <span>Sửa</span>
                                    </a>

                                    <form
                                        action="{{ route('admin.employee.destroy', $employee->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này không?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="admin-action-btn delete"
                                            title="Xóa nhân viên"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                            <span>Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-6">
                                Không có nhân viên nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($employees, 'links'))
            <div class="admin-table-footer">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-supplier-form-page">

    <div class="admin-page-header mb-4">
        <div>
            <a href="{{ route('admin.supplier.index') }}" class="admin-back-link admin-badge admin-badge-gray">
                ← Quay lại danh sách
            </a>
            <h1 class="admin-page-title mt-2 mb-0">Thêm nhà cung cấp</h1>
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
            <form method="POST" action="{{ route('admin.supplier.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Tên công ty</label>
                        <input
                            type="text"
                            name="name"
                            class="admin-input"
                            value="{{ old('name') }}"
                            placeholder="Nhập tên công ty"
                        >
                    </div>

                    <div>
                        <label class="admin-label">Tên người liên hệ <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="supplier_name"
                            class="admin-input"
                            value="{{ old('supplier_name') }}"
                            placeholder="Nhập tên người liên hệ"
                            required
                        >
                    </div>

                    <div>
                        <label class="admin-label">Chức vụ</label>
                        <input
                            type="text"
                            name="position"
                            class="admin-input"
                            value="{{ old('position') }}"
                            placeholder="Nhập chức vụ"
                        >
                    </div>

                    <div>
                        <label class="admin-label">Số điện thoại</label>
                        <input
                            type="text"
                            name="phone"
                            class="admin-input"
                            value="{{ old('phone') }}"
                            placeholder="Nhập số điện thoại"
                        >
                    </div>

                    <div>
                        <label class="admin-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="admin-input"
                            value="{{ old('email') }}"
                            placeholder="Nhập email"
                        >
                    </div>

                    <div>
                        <label class="admin-label">Địa chỉ</label>
                        <input
                            type="text"
                            name="address"
                            class="admin-input"
                            value="{{ old('address') }}"
                            placeholder="Nhập địa chỉ"
                        >
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-admin-pink">
                        Thêm mới
                    </button>

                    <a href="{{ route('admin.supplier.index') }}" class="btn-admin-light">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
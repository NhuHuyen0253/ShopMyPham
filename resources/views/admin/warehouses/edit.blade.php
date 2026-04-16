@extends('admin.layout')

@section('content')
<div class="p-4 admin-page">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="admin-page-header mb-4">
            <div>
                <h1 class="admin-page-title">Sửa kho</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                  <a href="{{ route('admin.warehouses.index') }}" class="btn-admin-soft-pink admin-header-btn">
                    <i class="fas fa-warehouse"></i>
                    <span>Danh sách kho</span>
                </a>
            </div>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="admin-alert admin-alert-danger mb-4">
                <div class="font-bold mb-2">Có lỗi xảy ra, vui lòng kiểm tra lại:</div>
                <ul class="mb-0 pl-5 list-disc">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <div class="admin-card">
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.warehouses.update', $warehouse) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="admin-label">
                            Tên kho <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="admin-input"
                            placeholder="Ví dụ: Kho Cần Thơ"
                            value="{{ old('name', $warehouse->name) }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="location" class="admin-label">Địa điểm</label>
                        <input
                            id="location"
                            name="location"
                            type="text"
                            class="admin-input"
                            placeholder="Ví dụ: Ninh Kiều, Cần Thơ"
                            value="{{ old('location', $warehouse->location) }}"
                        >
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="btn-admin-pink">
                            <i class="fas fa-save me-2"></i>Cập nhật kho
                        </button>

                        <a href="{{ route('admin.warehouses.index') }}" class="btn-admin-light">
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
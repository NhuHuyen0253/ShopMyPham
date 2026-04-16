@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-supplier-page">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="admin-page-header mb-4">
            <div>
                <h1 class="admin-page-title">Danh sách kho</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.stock.index') }}" class="btn-admin-light admin-header-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại quản lý kho</span>
                </a>

                <a href="{{ route('admin.warehouses.create') }}" class="btn-admin-pink admin-header-btn">
                    <i class="fas fa-plus"></i>
                    <span>Thêm kho</span>
                </a>

                <a href="{{ route('admin.stock_levels.index') }}" class="btn-admin-soft-pink admin-header-btn">
                    <i class="fas fa-boxes"></i>
                    <span>Quản lý tồn</span>
                </a>
            </div>
        </div>

        {{-- Alert success --}}
        @if (session('success'))
            <div class="admin-alert admin-alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search --}}
        <div class="admin-card mb-4">
            <div class="admin-card-body">
                <form method="GET" action="{{ route('admin.warehouses.index') }}">
                    <div class="admin-search-wrap">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Tìm theo tên kho hoặc địa điểm"
                            class="admin-search-input"
                        >

                        <button type="submit" class="btn-admin-pink admin-search-btn">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>

                        @if($q)
                            <a href="{{ route('admin.warehouses.index') }}" class="btn-admin-light">
                                Xóa lọc
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="admin-table-wrap">
            <div class="admin-table-toolbar">
                <div class="admin-table-title">Danh sách kho hàng</div>
                <div class="admin-table-count">
                    Tổng:
                    <strong>{{ method_exists($warehouses, 'total') ? number_format($warehouses->total()) : $warehouses->count() }}</strong>
                    kho
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-left">Tên kho</th>
                            <th class="text-left">Địa điểm</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $w)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-800">{{ $w->name }}</div>
                                </td>
                                <td>
                                    <span class="text-gray-600">{{ $w->location ?: 'Chưa cập nhật' }}</span>
                                </td>
                                <td>
                                    <div class="admin-action-group">
                                        <a href="{{ route('admin.warehouses.edit', $w) }}" class="admin-action-btn edit">
                                            <i class="fas fa-pen me-1"></i>Sửa
                                        </a>

                                        <form action="{{ route('admin.warehouses.destroy', $w) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Xoá kho này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-btn delete">
                                                <i class="fas fa-trash me-1"></i>Xoá
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-gray-500 py-6">
                                    Chưa có kho nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="admin-table-footer">
                {{ $warehouses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
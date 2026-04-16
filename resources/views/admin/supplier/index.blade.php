@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-supplier-page">

    {{-- Header --}}
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Danh sách nhà cung cấp</h1>
        </div>

        <a href="{{ route('admin.supplier.create') }}" class="btn-admin-pink admin-header-btn">
            <i class="fas fa-plus-circle"></i>
            <span>Thêm nhà cung cấp</span>
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
            <form method="GET" action="{{ route('admin.supplier.index') }}">
                <div class="admin-search-wrap">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $q ?? '') }}"
                        placeholder="Tìm theo tên, nhà cung cấp, email, số điện thoại..."
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
            <div class="admin-table-title">Danh sách nhà cung cấp</div>
            <div class="admin-table-count">
                Tổng:
                <strong>
                    {{ method_exists($suppliers, 'total') ? number_format($suppliers->total()) : count($suppliers) }}
                </strong>
                nhà cung cấp
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người liên hệ</th>
                        <th>Tên nhà cung cấp</th>
                        <th>Chức vụ</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($suppliers as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td class="font-semibold text-gray-800">{{ $s->name ?? '—' }}</td>
                            <td>{{ $s->supplier_name ?? '—' }}</td>
                            <td>{{ $s->position ?? '—' }}</td>
                            <td>{{ $s->phone ?? '—' }}</td>
                            <td>{{ $s->email ?? '—' }}</td>
                            <td class="admin-supplier-address">{{ $s->address ?? '—' }}</td>
                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.supplier.edit', $s) }}" class="admin-action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        <span>Sửa</span>
                                    </a>

                                    <form action="{{ route('admin.supplier.destroy', $s) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="admin-action-btn delete"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')"
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
                                Không có dữ liệu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($suppliers, 'links'))
            <div class="admin-table-footer">
                {{ $suppliers->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
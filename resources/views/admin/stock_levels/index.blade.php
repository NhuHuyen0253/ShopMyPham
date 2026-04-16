@extends('admin.layout')

@section('content')
<div class="p-4 admin-page">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="admin-page-header mb-4">
            <div>
                <h1 class="admin-page-title">Quản lý tồn kho</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.stock.index') }}" class="btn-admin-light admin-header-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại quản lý kho</span>
                </a>

                <a href="{{ route('admin.stock_levels.create') }}" class="btn-admin-pink admin-header-btn">
                    <i class="fas fa-plus"></i>
                    <span>Thêm tồn kho</span>
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="admin-alert admin-alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="admin-alert admin-alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Search --}}
        <div class="admin-card mb-4">
            <div class="admin-card-body">
                <form method="GET" action="{{ route('admin.stock_levels.index') }}">
                    <div class="admin-search-wrap">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q ?? '' }}"
                            class="admin-search-input"
                            placeholder="Tìm theo tên sản phẩm hoặc tên kho"
                        >

                        <button type="submit" class="btn-admin-pink admin-search-btn">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>

                        @if(!empty($q))
                            <a href="{{ route('admin.stock_levels.index') }}" class="btn-admin-light">
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
                <div class="admin-table-title">Danh sách tồn kho</div>
                <div class="admin-table-count">
                    Tổng:
                    <strong>{{ method_exists($stockLevels, 'total') ? number_format($stockLevels->total()) : $stockLevels->count() }}</strong>
                    dòng tồn kho
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Kho</th>
                            <th>Số lượng</th>
                            <th>Mức cảnh báo</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockLevels as $stock)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-800">
                                        {{ $stock->product->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID SP: {{ $stock->product_id }}
                                    </div>
                                </td>

                                <td>
                                    <div class="font-semibold text-gray-800">
                                        {{ $stock->warehouse->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID kho: {{ $stock->warehouse_id }}
                                    </div>
                                </td>

                                <td>
                                    <span class="font-semibold {{ (int)$stock->quantity <= (int)$stock->reorder_point ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ (int)$stock->quantity }}
                                    </span>
                                </td>

                                <td>{{ (int)$stock->reorder_point }}</td>

                                <td>
                                    @if((int)$stock->quantity <= 0)
                                        <span class="admin-badge admin-badge-red">Hết hàng</span>
                                    @elseif((int)$stock->quantity <= (int)$stock->reorder_point)
                                        <span class="admin-badge admin-badge-yellow">Sắp hết</span>
                                    @else
                                        <span class="admin-badge admin-badge-green">Ổn</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="admin-action-group">
                                        <a href="{{ route('admin.stock_levels.edit', $stock) }}" class="admin-action-btn edit">
                                            <i class="fas fa-pen me-1"></i>Sửa
                                        </a>

                                        <form action="{{ route('admin.stock_levels.destroy', $stock) }}"
                                              method="POST"
                                              onsubmit="return confirm('Xóa dòng tồn kho này?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-btn delete">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-6">
                                    Chưa có dữ liệu tồn kho.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="admin-table-footer">
                {{ $stockLevels->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
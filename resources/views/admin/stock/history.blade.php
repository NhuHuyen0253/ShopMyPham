@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-stock-page">

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

    <div class="admin-page-header mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="admin-page-title">Toàn bộ lịch sử kho</h1>
        </div>

        <a href="{{ route('admin.stock.index') }}" class="btn-admin-light">
            ← Quay lại quản lý kho
        </a>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.stock.history') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="admin-label">Từ khóa tìm kiếm</label>
                        <input
                            type="text"
                            name="q"
                            class="admin-input"
                            value="{{ $q }}"
                            placeholder="Tên SP, SKU, mã tham chiếu, ghi chú, NCC, kho..."
                        >
                    </div>

                    <div>
                        <label class="admin-label">Loại lịch sử</label>
                        <select name="type" class="admin-select">
                            <option value="">-- Tất cả --</option>
                            <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Nhập kho</option>
                            <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Xuất kho</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-admin-pink">Tìm kiếm</button>
                        <a href="{{ route('admin.stock.history') }}" class="btn-admin-light">Đặt lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-table-wrap">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">
                Danh sách lịch sử kho
            </div>

            <div class="admin-table-count">
                Tổng:
                <strong>{{ $movements->total() }}</strong>
                bản ghi
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Sản phẩm</th>
                        <th>SKU</th>
                        <th>Kho</th>
                        <th>Loại</th>
                        <th>Số lượng</th>
                        <th>Giá nhập</th>
                        <th>NCC</th>
                        <th>Mã tham chiếu</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ optional($movement->moved_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $movement->product->name ?? '—' }}</td>
                            <td>{{ $movement->product->sku ?? '—' }}</td>
                            <td>{{ $movement->warehouse->name ?? ('Kho #' . $movement->warehouse_id) }}</td>
                            <td>
                                @if($movement->type === 'in')
                                    <span class="admin-badge admin-badge-green">Nhập</span>
                                @elseif($movement->type === 'out')
                                    <span class="admin-badge admin-badge-red">Xuất</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">{{ $movement->type }}</span>
                                @endif
                            </td>
                            <td>{{ (int) $movement->quantity }}</td>
                            <td>{{ number_format((float) ($movement->unit_cost ?? 0), 0, ',', '.') }} đ</td>
                            <td>{{ $movement->supplier->name ?? '—' }}</td>
                            <td>{{ $movement->reference_code ?? '—' }}</td>
                            <td>{{ $movement->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-gray-500 py-6">
                                Không tìm thấy lịch sử phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-table-footer">
            {{ $movements->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
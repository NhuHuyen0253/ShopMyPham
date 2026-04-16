@extends('admin.layout')

@section('content')
<div class="p-4 admin-page">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="admin-page-header mb-4">
            <div>
                <h1 class="admin-page-title">Thêm tồn kho mới</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.stock_levels.index') }}" class="btn-admin-light admin-header-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại danh sách tồn kho</span>
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
                <form method="POST" action="{{ route('admin.stock_levels.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="product_id" class="admin-label">
                            Sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id" class="admin-select" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="warehouse_id" class="admin-label">
                            Kho <span class="text-red-500">*</span>
                        </label>
                        <select name="warehouse_id" id="warehouse_id" class="admin-select" required>
                            <option value="">-- Chọn kho --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="quantity" class="admin-label">
                            Số lượng tồn <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="admin-input"
                            min="0"
                            value="{{ old('quantity', 0) }}"
                            placeholder="Nhập số lượng tồn"
                            required
                        >
                    </div>

                    <div>
                        <label for="reorder_point" class="admin-label">Mức cảnh báo</label>
                        <input
                            type="number"
                            id="reorder_point"
                            name="reorder_point"
                            class="admin-input"
                            min="0"
                            value="{{ old('reorder_point', 0) }}"
                            placeholder="Ví dụ: 5"
                        >
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="btn-admin-pink">
                            <i class="fas fa-save me-2"></i>Lưu tồn kho
                        </button>

                        <a href="{{ route('admin.stock_levels.index') }}" class="btn-admin-light">
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
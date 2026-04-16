@extends('admin.layout')

@section('content')
<div class="p-4 admin-page admin-stock-page">

    @if(isset($lowStockProducts) && $lowStockProducts->count())
        <div class="admin-alert admin-alert-warning mb-4">
            <div class="font-bold mb-2">⚠️ Cảnh báo tồn kho</div>
            <ul class="mb-0 pl-4">
                @foreach($lowStockProducts as $product)
                    <li>
                        Sản phẩm <strong>{{ $product->name }}</strong> đang sắp hết hàng
                        (còn <strong>{{ (int) $product->quantity }}</strong>)
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

  

    <div class="admin-page-header mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="admin-page-title">Quản lý kho</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.warehouses.index') }}" class="btn-admin-light">
                Danh sách kho
            </a>

            <a href="{{ route('admin.warehouses.create') }}" class="btn-admin-pink">
                + Thêm kho mới
            </a>

            <a href="{{ route('admin.stock_levels.index') }}" class="btn-admin-soft-pink admin-header-btn">
                    <i class="fas fa-boxes"></i>
                    <span>Quản lý tồn</span>
            </a>
        </div>
    </div>

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

    @if ($errors->any())
        <div class="admin-alert admin-alert-danger mb-4">
            <div class="font-bold mb-1">Có lỗi xảy ra:</div>
            <ul class="mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bộ lọc --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.stock.index') }}">
                <div class="admin-search-wrap">
                    <input
                        type="text"
                        name="q"
                        class="admin-search-input"
                        value="{{ $q }}"
                        placeholder="Nhập tên sản phẩm hoặc SKU"
                    >
                    <button type="submit" class="btn-admin-pink admin-search-btn">Tìm</button>
                    <a href="{{ route('admin.stock.index') }}" class="btn-admin-light">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Danh sách tồn kho --}}
    <div class="admin-table-wrap mb-4">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">Danh sách tồn kho</div>
            <div class="admin-table-count">
                Tổng:
                <strong>{{ method_exists($products, 'total') ? number_format($products->total()) : count($products) }}</strong>
                sản phẩm
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>SKU</th>
                        <th>Kho mặc định</th>
                        <th>Giá nhập</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>NCC</th>
                        <th>Cảnh báo</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>#{{ $product->id }}</td>

                            <td>
                                <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                            </td>

                            <td>
                                @if(!$product->sku)
                                    <form action="{{ route('admin.stock.updateSku', ['id' => $product->id]) }}"
                                          method="POST"
                                          class="admin-inline-sku-form">
                                        @csrf
                                        @method('PATCH')

                                        <input type="text"
                                               name="sku"
                                               class="admin-input admin-input-sm"
                                               placeholder="Nhập SKU"
                                               required>

                                        <button class="admin-action-btn edit" type="submit">Lưu</button>
                                    </form>
                                @else
                                    <span class="admin-badge admin-badge-gray">{{ $product->sku }}</span>
                                @endif
                            </td>

                            <td>
                                @if($product->defaultWarehouse)
                                    <div class="font-semibold text-gray-800">{{ $product->defaultWarehouse->name }}</div>
                                    <div class="text-xs text-gray-500">ID kho: {{ $product->defaultWarehouse->id }}</div>
                                @else
                                    <span class="text-gray-500">Chưa gán kho</span>
                                @endif
                            </td>

                            <td>{{ number_format((float)($product->original_price ?? 0), 0, ',', '.') }} đ</td>
                            <td>{{ number_format((float)($product->price ?? 0), 0, ',', '.') }} đ</td>

                            <td>
                                <span class="font-semibold {{ (int)$product->quantity <= 5 ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ (int)$product->quantity }}
                                </span>
                            </td>

                            <td>{{ $product->supplier->name ?? '—' }}</td>

                            <td>
                                @if((int)$product->quantity <= 0)
                                    <span class="admin-badge admin-badge-red">Hết hàng</span>
                                @elseif((int)$product->quantity <= 5)
                                    <span class="admin-badge admin-badge-yellow">Sắp hết</span>
                                @else
                                    <span class="admin-badge admin-badge-green">Ổn</span>
                                @endif
                            </td>

                            <td>
                                <div class="admin-action-group">
                                    <button type="button"
                                            class="admin-action-btn edit btn-open-stockin"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}">
                                        Nhập kho
                                    </button>

                                    <button type="button"
                                            class="admin-action-btn delete btn-open-stockout"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-product-qty="{{ (int) $product->quantity }}">
                                        Xuất kho
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-gray-500 py-6">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-table-footer">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>

    {{-- Form nhập kho --}}
    <div class="admin-card mb-4 stockin-hidden" id="stockInCard">
        <div class="admin-card-body">
            <div class="admin-stockin-header mb-4">
                <div>
                    <div class="admin-section-title mb-1">Nhập kho sản phẩm</div>
                    <div class="admin-page-subtitle" id="selectedProductText">Chưa chọn sản phẩm</div>
                </div>

                <button type="button" class="btn-admin-light" id="btnCloseStockIn">
                    Đóng
                </button>
            </div>

            <form action="{{ route('admin.stock.in') }}" method="POST">
                @csrf

                <input type="stockin-hidden" name="product_id" id="stockInProductId">

                <div class="mb-4">
                    <label class="admin-label">Sản phẩm đang nhập kho</label>
                    <input type="text"
                           id="stockInProductName"
                           class="admin-input"
                           value=""
                           readonly>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="admin-label">Kho</label>
                        <select name="warehouse_id" class="admin-select" required>
                            <option value="">-- Chọn kho --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">
                                    {{ $warehouse->name ?? ('Kho #' . $warehouse->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="admin-label">Nhà cung cấp</label>
                        <select name="supplier_id" class="admin-select">
                            <option value="">-- Chọn nhà cung cấp --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name ?? ('NCC #' . $supplier->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="admin-label">Số lượng nhập</label>
                        <input type="number"
                               name="quantity"
                               class="admin-input"
                               min="1"
                               placeholder="Nhập số lượng"
                               required>
                    </div>

                    <div>
                        <label class="admin-label">Giá nhập</label>
                        <input type="number"
                               name="unit_cost"
                               class="admin-input"
                               min="0"
                               step="0.01"
                               placeholder="Ví dụ: 120000">
                    </div>

                    <div>
                        <label class="admin-label">Mã tham chiếu</label>
                        <input type="text"
                               name="reference_code"
                               class="admin-input"
                               placeholder="VD: NK-20260307-01">
                    </div>

                    <div>
                        <label class="admin-label">Ghi chú</label>
                        <input type="text"
                               name="note"
                               class="admin-input"
                               placeholder="Ghi chú nhập kho">
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-admin-pink">
                        + Xác nhận nhập kho
                    </button>
                    <button type="button" class="btn-admin-light" id="btnCancelStockIn">
                        Huỷ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Xuất kho --}}
    <div class="admin-card mb-4 stockin-hidden" id="stockOutCard">
        <div class="admin-card-body">
            <div class="admin-stockin-header mb-4">
                <div>
                    <div class="admin-section-title mb-1">Xuất kho</div>
                    <div class="admin-page-subtitle" id="selectedStockOutProductText">Chưa chọn sản phẩm</div>
                </div>

                <button type="button" class="btn-admin-light" id="btnCloseStockOut">
                    Đóng
                </button>
            </div>

            <form action="{{ route('admin.stock.out') }}" method="POST">
                @csrf

                <input type="hidden" name="product_id" id="stockOutProductId">

                <div class="mb-4">
                    <label class="admin-label">Sản phẩm đang xuất kho</label>
                    <input type="text"
                        id="stockOutProductName"
                        class="admin-input"
                        value=""
                        readonly>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="admin-label">Kho</label>
                        <select name="warehouse_id" class="admin-select" required>
                            <option value="">-- Chọn kho --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">
                                    {{ $warehouse->name ?? ('Kho #' . $warehouse->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="admin-label">Số lượng xuất</label>
                        <input type="number"
                            name="quantity"
                            class="admin-input"
                            min="1"
                            placeholder="Nhập số lượng xuất"
                            required>
                    </div>

                    <div>
                        <label class="admin-label">Mã tham chiếu</label>
                        <input type="text"
                            name="reference_code"
                            class="admin-input"
                            placeholder="VD: XK-20260409-01">
                    </div>

                    <div class="md:col-span-3">
                        <label class="admin-label">Ghi chú</label>
                        <input type="text"
                            name="note"
                            class="admin-input"
                            placeholder=" Bán trực tiếp tại cửa hàng">
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-admin-pink">
                        + Xác nhận xuất kho
                    </button>
                    <button type="button" class="btn-admin-light" id="btnCancelStockOut">
                        Huỷ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lịch sử nhập xuất --}}
    <div class="admin-table-wrap mt-4">
        <div class="admin-table-toolbar">
            <div class="admin-table-title">Lịch sử nhập / xuất gần đây</div>
             <a href="{{ route('admin.stock.history', ['type' => 'in']) }}" class="btn-admin-pink">
                Xem toàn bộ lịch sử nhập kho
             </a>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Sản phẩm</th>
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
                    @forelse($recentMovements as $movement)
                        <tr>
                            <td>{{ optional($movement->moved_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $movement->product->name ?? '—' }}</td>
                            <td>{{ $movement->warehouse->name ?? ('Kho #' . $movement->warehouse_id) }}</td>
                            <td>
                                @if($movement->type === 'in')
                                    <span class="admin-badge admin-badge-green">Nhập</span>
                                @else
                                    <span class="admin-badge admin-badge-red">Xuất</span>
                                @endif
                            </td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ number_format((float)($movement->unit_cost ?? 0), 0, ',', '.') }} đ</td>
                            <td>{{ $movement->supplier->name ?? '—' }}</td>
                            <td>{{ $movement->reference_code ?? '—' }}</td>
                            <td>{{ $movement->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-6">Chưa có lịch sử nhập xuất.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('js/stock.js') }}?v={{ time() }}" defer></script>
@endsection
@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Danh sách sản phẩm</h1>    
        </div>

        <a href="{{ route('admin.product.create') }}" class="btn-admin-pink">
            + Thêm sản phẩm
        </a>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[260px]">
                    <label class="admin-label">Tìm kiếm</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="admin-input">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-admin-pink">Tìm</button>

                    @if(request('q'))
                        <a href="{{ url()->current() }}" class="btn-admin-light">Xoá lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="admin-table-wrap">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Giá bán</th>
                        <th>Giá gốc</th>
                        <th>Giá hiện tại</th>
                        <th>Hot Deal</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $baseCost = $product->original_price;
                            $listPrice = $product->price;
                            $isHotdeal = (bool) $product->is_hotdeal;
                            $discountPercent = $product->discount_percent ?? 0;

                            $currentPrice = $listPrice;
                            if ($isHotdeal && $discountPercent > 0 && $listPrice !== null) {
                                $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                            }
                        @endphp

                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('images/product/'.$product->image) }}" class="h-14 w-20 object-contain rounded bg-white border p-1">
                                @else
                                    <span class="text-gray-400 text-sm">Chưa có ảnh</span>
                                @endif
                            </td>

                            <td>
                                <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                @if($isHotdeal)
                                    <span class="admin-badge admin-badge-red mt-2">Hot Deal</span>
                                @endif
                            </td>

                            <td>{{ $listPrice ? number_format($listPrice,0,',','.') . '₫' : '—' }}</td>
                            <td class="text-gray-500">{{ $baseCost ? number_format($baseCost,0,',','.') . '₫' : '—' }}</td>

                            <td>
                                @if($isHotdeal && $discountPercent > 0)
                                    <div class="font-bold text-pink-600">{{ number_format($currentPrice,0,',','.') }}₫</div>
                                    <div class="text-xs text-gray-500">-{{ $discountPercent }}%</div>
                                @else
                                    {{ number_format($currentPrice,0,',','.') }}₫
                                @endif
                            </td>

                            <td>
                                @if($isHotdeal && $discountPercent > 0)
                                    <span class="admin-badge admin-badge-red">{{ $discountPercent }}%</span>
                                @else
                                    <span class="admin-badge admin-badge-gray">Không</span>
                                @endif
                            </td>

                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.product.edit', $product) }}" class="admin-action-btn edit">Sửa</a>

                                    <form action="{{ route('admin.product.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn xoá?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-action-btn delete" type="submit">Xoá</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection
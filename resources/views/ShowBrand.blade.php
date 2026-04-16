@extends('layout')

@section('content')
<div class="container py-4 py-md-5">

    {{-- Tiêu đề --}}
    <div class="mb-4 mb-md-5">
        <h2 class="fw-bold mb-2">Sản phẩm của thương hiệu {{ $brands->name }}</h2>
    </div>

    @if($products->count())
        <div class="row g-3 g-md-4">
            @forelse($products as $product)
                @php
                    $img = $product->image
                        ? asset('images/product/' . ltrim($product->image, '/'))
                        : $fallbackSvg;

                    $detailUrl = route('product.show', $product->id);

                    $listPrice = $product->price;
                    $isHotdeal = (bool) ($product->is_hotdeal ?? false);
                    $discountPercent = (int) ($product->discount_percent ?? 0);

                    $currentPrice = $listPrice;
                    if ($isHotdeal && $discountPercent > 0 && $listPrice !== null) {
                        $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                    }

                    $qty = (int) ($product->quantity ?? 0);
                @endphp

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm product-card rounded-4 overflow-hidden bg-white">
                        <div class="position-relative">
                            <a href="{{ $detailUrl }}" class="text-decoration-none">
                                <div class="ratio ratio-1x1 bg-light">
                                    <img
                                        src="{{ $img }}"
                                        alt="{{ $product->name }}"
                                        class="w-100 h-100 object-fit-cover">
                                </div>
                            </a>

                            @if($qty <= 0)
                                <span class="badge bg-secondary position-absolute top-0 start-0 m-2 px-3 py-2">
                                    Tạm hết hàng
                                </span>
                            @elseif($isHotdeal && $discountPercent > 0)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-3 py-2">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column text-center">
                            <a href="{{ $detailUrl }}" class="text-decoration-none text-dark">
                                <h5 class="product-title mb-3 text-sm font-bold leading-6 text-gray-800 transition group-hover:text-pink-600 md:text-base">
                                    {{ $product->name }}
                                </h5>
                            </a>

                            <div class="mb-3">
                                @if($isHotdeal && $discountPercent > 0 && $listPrice !== null)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-muted text-decoration-line-through small">
                                            {{ number_format($listPrice, 0, ',', '.') }} đ
                                        </span>
                                        <span class="text-danger fw-bold fs-4">
                                            {{ number_format($currentPrice, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                @else
                                    <span class="text-danger fw-bold fs-4">
                                        {{ $listPrice !== null ? number_format($listPrice, 0, ',', '.') . ' đ' : 'Liên hệ' }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <a href="{{ $detailUrl }}"
                                   class="inline-flex w-full items-center justify-center rounded-2xl border !border-sky-500 !bg-white px-4 py-2.5 text-sm font-semibold !text-sky-600 shadow-sm transition-all duration-200 hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <p class="text-muted fs-5 mb-0">
                            Chưa có sản phẩm nào cho thương hiệu này.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <p class="text-muted fs-5 mb-0">
                Chưa có sản phẩm nào cho thương hiệu này.
            </p>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('brands') }}" class="btn btn-secondary">
            ← Quay lại danh sách thương hiệu
        </a>
    </div>
</div>
@endsection
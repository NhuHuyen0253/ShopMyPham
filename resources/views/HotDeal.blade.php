@extends('layout')

@section('title', $promotion ? $promotion->name . ' - Khuyến mãi' : 'Hot Deal - Khuyến mãi sốc')

@section('content')

<div class="container py-4 py-md-5">

    <div class="text-center mb-4 mb-md-5">
        @if($promotion)
            <span class="badge rounded-pill px-3 py-2 text-bg-danger mb-3 fs-6 shadow-sm">
                KHUYẾN MÃI
            </span>
            <h2 class="fw-bold text-danger mb-2">🎉 {{ $promotion->name }}</h2>
            <p class="text-muted mb-0">
                {{ $promotion->description ?: 'Khám phá các sản phẩm thuộc chương trình khuyến mãi này' }}
            </p>
        @else
            <span class="badge rounded-pill px-3 py-2 text-bg-danger mb-3 fs-6 shadow-sm">
                HOT DEAL
            </span>
            <h2 class="fw-bold text-danger mb-2">🔥 Ưu đãi HOT</h2>
            <p class="text-muted mb-0">
                Khám phá các sản phẩm đang giảm giá hấp dẫn hôm nay
            </p>
        @endif
    </div>

    @if($products->count())
        <div class="row g-3 g-md-4">
            @foreach($products as $product)
                @php
                    $thumb = $product->images->first();

                    $imgUrl = $thumb
                        ? ($thumb->url ?? \Illuminate\Support\Facades\Storage::url(trim($thumb->path, '/') . '/' . $thumb->file_name))
                        : ($product->image
                            ? asset('images/product/' . ltrim($product->image, '/'))
                            : ($product->image_url ?? asset('images/product/placeholder.jpg')));

                    $listPrice = $product->price;
                    $stockQty = (int)($product->quantity ?? 0);
                    $detailUrl = route('product.show', $product->id);

                    if ($promotion) {
                        $appliedPromotion = $product->promotions->first();
                        $discountPercent = (int) ($appliedPromotion->discount_percent ?? 0);
                        $isDiscounted = $discountPercent > 0;
                    } else {
                        $discountPercent = (int) ($product->discount_percent ?? 0);
                        $isDiscounted = (bool) ($product->is_hotdeal ?? false) && $discountPercent > 0;
                    }

                    $currentPrice = $listPrice;
                    if ($isDiscounted && $listPrice !== null) {
                        $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                    }
                @endphp

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm product-card rounded-4 overflow-hidden bg-white">
                        <div class="position-relative">
                            <a href="{{ $detailUrl }}" class="text-decoration-none">
                                <div class="ratio ratio-1x1 bg-light">
                                    <img
                                        src="{{ $imgUrl }}"
                                        alt="{{ $product->name }}"
                                        class="w-100 h-100 object-fit-cover">
                                </div>
                            </a>

                            @if($discountPercent > 0)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-3 py-2">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif

                            @if($stockQty <= 0)
                                <span class="badge bg-secondary position-absolute top-0 end-0 m-2 px-3 py-2">
                                    Hết hàng
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column text-center p-3">
                            <a href="{{ $detailUrl }}" class="text-decoration-none text-dark">
                                <h5 class="fw-bold mb-3 product-title">
                                    {{ $product->name }}
                                </h5>
                            </a>

                            <div class="mb-3">
                                @if($isDiscounted && $listPrice !== null)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-muted text-decoration-line-through">
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
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <p class="text-muted fs-5 mb-0">
                Hiện chưa có sản phẩm phù hợp trong chương trình này 🥹
            </p>
        </div>
    @endif
</div>
@endsection
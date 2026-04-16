@extends('layout')

@section('content')

@if ($banners->count())
<section class="py-6 md:py-8">
    <div class="container mx-auto px-3">
        <div id="promoCarousel"
             class="carousel slide relative"
             data-bs-ride="carousel"
             data-bs-interval="3000"
             data-bs-pause="false">

            <div class="carousel-indicators absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                @foreach($banners as $key => $b)
                    <button type="button"
                            data-bs-target="#promoCarousel"
                            data-bs-slide-to="{{ $key }}"
                            class="{{ $key === 0 ? 'active' : '' }} h-3 w-3 rounded-full border-0 bg-white/80 transition hover:bg-white"
                            aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $key + 1 }}">
                    </button>
                @endforeach
            </div>

            <div class="carousel-inner overflow-hidden rounded-4 shadow-[0_14px_35px_rgba(255,105,180,0.18)]">
                @foreach($banners as $key => $b)
                    @php
                        $bannerUrl = !empty($b->promotion_id)
                            ? route('hotdeal', ['promotion' => $b->promotion_id])
                            : (!empty($b->button_link) ? $b->button_link : '#');
                    @endphp

                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                        <div class="group relative overflow-hidden rounded-4 bg-pink-50">
                            <a href="{{ $bannerUrl }}" class="block">
                                <img
                                    src="{{ asset('storage/' . $b->image) }}"
                                    alt="{{ $b->name }}"
                                    class="block w-full object-contain transition duration-500 group-hover:scale-[1.02]
                                        h-[450px] sm:h-[550px] md:h-[650px] lg:h-[720px]"
                                >
                            </a>

                            @if(!empty($b->name))
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/55 via-black/15 to-transparent px-4 py-6 md:px-8 md:py-8">
                                    <h2 class="text-xl font-bold text-white md:text-3xl lg:text-4xl">
                                        {{ $b->name }}
                                    </h2>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($banners->count() > 1)
                <button class="carousel-control-prev custom-arrow custom-arrow-left"
                        type="button"
                        data-bs-target="#promoCarousel"
                        data-bs-slide="prev"
                        aria-label="Banner trước">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button class="carousel-control-next custom-arrow custom-arrow-right"
                        type="button"
                        data-bs-target="#promoCarousel"
                        data-bs-slide="next"
                        aria-label="Banner tiếp theo">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ====== SẢN PHẨM MỚI THÊM ====== --}}
<section class="py-8 md:py-12">
    <div class="container mx-auto px-3">

        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h3 class="mb-0 text-2xl font-extrabold text-gray-800 md:text-3xl">
                    Sản phẩm mới
                </h3>
            </div>

            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-1 rounded-full border border-pink-200 bg-white px-4 py-2 text-sm font-semibold text-pink-600 shadow-sm transition hover:-translate-y-0.5 hover:border-pink-300 hover:bg-pink-50 hover:text-pink-700">
                Xem tất cả
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @php
            $fallbackSvg = "data:image/svg+xml;utf8," .
                "<svg xmlns='http://www.w3.org/2000/svg' width='300' height='300'>" .
                "<rect width='100%' height='100%' fill='%23fdf2f8'/>" .
                "<text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' " .
                "fill='%23c08497' font-size='14'>no image</text></svg>";
        @endphp

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @forelse($products as $product)
                @php
                    $thumb = $product->images->first();

                    $img = $product->image
                            ? asset('images/product/' . ltrim($product->image, '/'))
                            : $fallbackSvg;

                    $detailUrl = route('product.show', $product->id);

                    $listPrice = $product->price;
                    $isHotdeal = (bool) $product->is_hotdeal;
                    $discountPercent = $product->discount_percent ?? null;

                    $currentPrice = $listPrice;
                    if ($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null) {
                        $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                    }

                    $qty = (int) ($product->quantity ?? 0);
                @endphp

                <div>
                    <div class="group flex h-full flex-col overflow-hidden rounded-3xl border border-pink-100 bg-white shadow-[0_8px_24px_rgba(255,105,180,0.12)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_36px_rgba(255,105,180,0.2)]">
                        <div class="relative overflow-hidden">
                            <a href="{{ $detailUrl }}" class="block">
                                <img src="{{ $img }}"
                                     alt="{{ $product->name }}"
                                     class="h-[190px] w-full object-cover transition duration-500 group-hover:scale-105 md:h-[230px]">
                            </a>

                            @if($qty <= 0)
                                <span class="absolute left-3 top-3 rounded-full bg-gray-500 px-3 py-1 text-xs font-bold text-white shadow">
                                    Tạm hết hàng
                                </span>
                            @elseif($isHotdeal && $discountPercent && $discountPercent > 0)
                                <span class="absolute left-3 top-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-bold text-white shadow">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-4 text-center">
                            <a href="{{ $detailUrl }}" class="text-gray-800 no-underline">
                                <div class="min-h-[48px] md:min-h-[56px] flex items-start justify-center">
                                    <h5 class="product-title text-sm font-bold leading-6 text-gray-800 transition group-hover:text-pink-600 md:text-base">
                                        {{ $product->name }}
                                    </h5>
                                </div>
                            </a>

                            <div class="mt-3">
                                @if($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null)
                                    <div class="space-y-1">
                                        <p class="text-lg font-extrabold text-rose-600 md:text-xl">
                                            {{ number_format($currentPrice, 0, ',', '.') }} đ
                                        </p>
                                        
                                    </div>
                                @else
                                    <p class="text-lg font-extrabold text-rose-600 md:text-xl">
                                        {{ !is_null($listPrice) ? number_format((int)$listPrice, 0, ',', '.') . ' đ' : 'Liên hệ' }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-4">
                                <a href="{{ $detailUrl }}"
                                    class="inline-flex w-full items-center justify-center rounded-2xl border !border-sky-500 !bg-white px-4 py-2.5 text-sm font-semibold !text-sky-600 shadow-sm transition-all duration-200 hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-pink-100 bg-white px-6 py-10 text-center text-gray-500 shadow-sm">
                    Hiện chưa có sản phẩm nào.
                </div>
            @endforelse
        </div>

    </div>
</section>

@endsection
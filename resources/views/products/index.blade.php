@extends('layout')

@section('content')

<div class="container mx-auto px-3 py-6 md:py-10">

    <div class="mb-6">
        <h2 class="mb-4 text-2xl font-extrabold text-gray-800 md:text-3xl">
            Kết quả tìm kiếm
        </h2>
    </div>

    @php
        $fallbackSvg = "data:image/svg+xml;utf8," .
            "<svg xmlns='http://www.w3.org/2000/svg' width='300' height='300'>" .
            "<rect width='100%' height='100%' fill='%23fdf2f8'/>" .
            "<text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' " .
            "fill='%239ca3af' font-size='14'>no image</text></svg>";
    @endphp

    @if($products->count())
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $product)
                @php
                    $qty = (int) ($product->quantity ?? 0);

                    $thumb = $product->images->first();

                    $img = $thumb
                        ? ($thumb->url ?? \Illuminate\Support\Facades\Storage::url(trim($thumb->path, '/') . '/' . $thumb->file_name))
                        : ($product->image
                            ? asset('images/product/' . ltrim($product->image, '/'))
                            : ($product->image_url ?? $fallbackSvg));

                    $detailUrl = route('product.show', $product->id);

                    $listPrice = $product->price;
                    $isHotdeal = (bool) $product->is_hotdeal;
                    $discountPercent = $product->discount_percent ?? null;

                    $currentPrice = $listPrice;
                    if ($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null) {
                        $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                    }
                @endphp

                <div>
                    <div class="group flex h-full flex-col overflow-hidden rounded-3xl border border-pink-100 bg-white shadow-[0_8px_24px_rgba(255,105,180,0.12)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_36px_rgba(255,105,180,0.18)]">
                        
                        <div class="relative overflow-hidden bg-white">
                            <a href="{{ $detailUrl }}" class="block no-underline">
                                <img
                                    src="{{ $img }}"
                                    alt="{{ $product->name }}"
                                    class="h-[190px] w-full object-contain p-3 transition duration-300 group-hover:scale-105"
                                >
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
                            <a href="{{ $detailUrl }}" class="no-underline text-gray-800">
                                <h5 class="product-title text-sm font-semibold leading-6 text-gray-800 transition group-hover:text-pink-600 md:text-base">
                                    {{ $product->name }}
                                </h5>
                            </a>

                            @if(!empty($product->short_description))
                                <p class="mt-2 min-h-[40px] overflow-hidden text-xs leading-5 text-gray-500 md:text-sm">
                                    {{ $product->short_description }}
                                </p>
                            @endif

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

                            <div class="mt-auto pt-4">
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

        <div class="mt-6 flex justify-center">
            {{ $products->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="rounded-3xl border border-pink-100 bg-white px-6 py-12 text-center shadow-sm">
            <p class="mb-0 text-base text-gray-500 md:text-lg">
                Không tìm thấy sản phẩm nào phù hợp.
            </p>
        </div>
    @endif
</div>
@endsection
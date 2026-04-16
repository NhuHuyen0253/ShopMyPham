@extends('layout')

@section('content')
<div class="container mx-auto px-3 py-6 md:py-8">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">

        {{-- Sidebar --}}
        <aside class="md:col-span-3">
            <div class="rounded-2xl border border-pink-100 bg-white p-4 shadow-sm">
                <h5 class="mb-4 text-lg font-extrabold uppercase tracking-wide text-gray-800">
                    Chăm sóc tóc
                </h5>

                <ul class="space-y-2">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('hair.category', $category->slug) }}"
                               class="block rounded-3xl border border-pink-100 bg-pink-50 px-4 py-3 text-base font-semibold text-gray-700 transition hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        {{-- Main content --}}
        <section class="md:col-span-9">
            @if ($products->count())
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                    @forelse ($products as $product)
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

                        <div>
                            <div class="group flex h-full flex-col overflow-hidden rounded-3xl border border-pink-100 bg-white shadow-[0_8px_24px_rgba(255,105,180,0.12)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_36px_rgba(255,105,180,0.18)]">
                                
                                <div class="relative overflow-hidden bg-white">
                                    <a href="{{ $detailUrl }}" class="block no-underline">
                                        <div class="aspect-square bg-gray-50">
                                            <img
                                                src="{{ $img }}"
                                                alt="{{ $product->name }}"
                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                        </div>
                                    </a>

                                    @if($qty <= 0)
                                        <span class="absolute left-3 top-3 rounded-full bg-gray-500 px-3 py-1 text-xs font-bold text-white shadow">
                                            Tạm hết hàng
                                        </span>
                                    @elseif($isHotdeal && $discountPercent > 0)
                                        <span class="absolute left-3 top-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-bold text-white shadow">
                                            -{{ $discountPercent }}%
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col p-4 text-center">
                                    <a href="{{ $detailUrl }}" class="no-underline text-gray-800">
                                        <h5 class="product-title mb-3 text-sm font-bold leading-6 text-gray-800 transition group-hover:text-pink-600 md:text-base">
                                            {{ $product->name }}
                                        </h5>
                                    </a>

                                    <div class="mb-4">
                                        @if($isHotdeal && $discountPercent > 0 && $listPrice !== null)
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-lg font-extrabold text-rose-600 md:text-xl">
                                                    {{ number_format($currentPrice, 0, ',', '.') }} đ
                                                </span>
                                           
                                            </div>
                                        @else
                                            <span class="text-lg font-extrabold text-rose-600 md:text-xl">
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
                        <div class="col-span-full rounded-3xl border border-pink-100 bg-white px-6 py-12 text-center shadow-sm">
                            <p class="my-0 text-base text-gray-500 md:text-lg">
                                Không có sản phẩm nào trong danh mục này.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Phân trang --}}
                <div class="mt-6 flex justify-center">
                    {{ $products->links('vendor.pagination.custom') }}
                </div>
            @else
                <div class="rounded-3xl border border-pink-100 bg-white px-6 py-12 text-center shadow-sm">
                    <p class="my-0 text-base text-gray-500 md:text-lg">
                        Không có sản phẩm nào trong danh mục này.
                    </p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/buynow.js') }}?v={{ file_exists(public_path('js/buynow.js')) ? filemtime(public_path('js/buynow.js')) : time() }}"></script>
@endsection
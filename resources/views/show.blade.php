@extends('layout')

@section('content')
<div class="container py-5">
    @php
        $placeholder = asset('images/product/placeholder.jpg');

        $mainImg = $product->image
            ? asset('images/product/' . ltrim($product->image, '/'))
            : $placeholder;

        $gallery = collect($product->images ?? [])->map(function ($i) {
            if (is_string($i)) {
                return asset('images/product/' . ltrim($i, '/'));
            }

            if (is_object($i)) {
                if (isset($i->url)) {
                    return $i->url;
                }

                if (isset($i->path, $i->file_name)) {
                    return \Illuminate\Support\Facades\Storage::url(trim($i->path, '/') . '/' . $i->file_name);
                }
            }

            return null;
        })->filter()->values();

        $gallery = collect([$mainImg])->merge($gallery)->unique()->values();

        $listPrice       = $product->price;
        $isHotdeal       = (bool) $product->is_hotdeal;
        $discountPercent = $product->discount_percent ?? null;

        $currentPrice = $listPrice;
        if ($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null) {
            $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
        }

        $stockQty = max(0, (int)($product->quantity ?? 0) - (int)($product->reserved_quantity ?? 0));
        $priceForBtn = preg_replace('/\D+/', '', (string) ($currentPrice ?? $product->price));
        $imgForBtn   = ($gallery[0] ?? $mainImg) ?? '';
    @endphp

    {{-- HÀNG 1: ẢNH + THÔNG TIN MUA HÀNG --}}
    <div class="row align-items-start g-5">
        <div class="col-lg-5">
            <div id="productGalleryCarousel"
                 class="carousel slide border rounded-3 overflow-hidden bg-white shadow-sm"
                 data-bs-ride="carousel"
                 data-bs-interval="3000">

                <div class="carousel-inner">
                    @foreach($gallery as $idx => $src)
                        <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                            <div class="d-flex align-items-center justify-content-center bg-white" style="height:420px;">
                                <img
                                    src="{{ $src }}"
                                    alt="{{ $product->name }} - ảnh {{ $idx + 1 }}"
                                    class="img-fluid"
                                    style="max-height:100%; object-fit:contain;"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($gallery->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#productGalleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                        <span class="visually-hidden">Trước</span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#productGalleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                        <span class="visually-hidden">Sau</span>
                    </button>
                @endif
            </div>

            @if($gallery->count() > 1)
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    @foreach($gallery as $idx => $src)
                        <button
                            type="button"
                            data-bs-target="#productGalleryCarousel"
                            data-bs-slide-to="{{ $idx }}"
                            class="border rounded-3 p-0 bg-white overflow-hidden {{ $idx === 0 ? 'border-danger' : '' }}"
                            aria-label="Ảnh {{ $idx + 1 }}"
                            style="width:72px; height:72px;"
                        >
                            <img
                                src="{{ $src }}"
                                alt="thumb {{ $idx + 1 }}"
                                style="width:100%; height:100%; object-fit:cover;"
                            >
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-7">
            <h2 class="fw-bold text-dark mb-3">{{ html_entity_decode($product->name) }}</h2>

            <div class="mb-3">
                @if($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null)
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted text-decoration-line-through fs-4">
                            {{ number_format($listPrice, 0, ',', '.') }} đ
                        </span>

                        <span class="price fs-2 text-danger fw-bold">
                            {{ number_format($currentPrice, 0, ',', '.') }} đ
                        </span>

                        <span class="badge bg-danger px-3 py-2">
                            -{{ $discountPercent }}%
                        </span>
                    </div>
                @else
                    <span class="price fs-2 text-danger fw-bold">
                        {{ $listPrice !== null ? number_format($listPrice, 0, ',', '.') . ' đ' : 'Liên hệ' }}
                    </span>
                @endif
            </div>

            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                @if($stockQty > 0)
                    <span class="badge bg-success fs-6 px-3 py-2">Còn hàng</span>
                @else
                    <span class="badge bg-secondary fs-6 px-3 py-2">Tạm hết hàng</span>
                @endif

                @auth
                    @if(!empty($isFavorited) && $isFavorited)
                        <span class="badge bg-danger-subtle text-danger border">Đã thêm vào yêu thích</span>
                    @endif
                @endauth
            </div>
            @if(!empty($capacityProducts) && $capacityProducts->count() > 0)
                <div class="mb-4">
                    <div class="fw-semibold text-dark mb-2">
                        Dung tích:
                        <span class="fw-bold">{{ $product->capacity }}ml</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                       @foreach($capacityProducts->sortBy(fn($item) => (int) $item->capacity) as $item)
                            <a href="{{ route('product.show', $item->id) }}"
                            class="capacity-chip text-decoration-none {{ $item->id == $product->id ? 'active' : '' }}">
                                {{ $item->capacity }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-3 mt-3 align-items-center">
                @if($stockQty > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart d-flex gap-2 align-items-center flex-wrap">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="number"
                               name="quantity"
                               value="1"
                               min="1"
                               max="{{ $stockQty }}"
                               class="form-control"
                               style="width:110px">
                        <button type="submit" class="btn btn-success px-4">Thêm vào giỏ hàng</button>
                    </form>

                    <button id="btnBuyNow"
                            type="button"
                            class="btn btn-pink px-5"
                            data-bs-toggle="modal"
                            data-bs-target="#buyNowModal"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $priceForBtn }}"
                            data-image="{{ $imgForBtn }}">
                        Mua ngay
                    </button>

                    @auth
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="btn {{ !empty($isFavorited) && $isFavorited ? 'btn-danger' : 'btn-outline-danger' }} px-4">
                                {{ !empty($isFavorited) && $isFavorited ? '♥ Đã yêu thích' : '♡ Yêu thích' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-outline-danger px-4">
                            ♡ Yêu thích
                        </a>
                    @endauth
                @else
                    <input type="number" value="0" class="form-control" style="width:110px" disabled>
                    <button type="button" class="btn btn-secondary px-4" disabled>Tạm hết hàng</button>
                    <button type="button" class="btn btn-secondary px-5" disabled>Mua ngay</button>

                    @auth
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="btn {{ !empty($isFavorited) && $isFavorited ? 'btn-danger' : 'btn-outline-danger' }} px-4">
                                {{ !empty($isFavorited) && $isFavorited ? '♥ Đã yêu thích' : '♡ Yêu thích' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-outline-danger px-4">
                            ♡ Yêu thích
                        </a>
                    @endauth
                @endif
            </div>

            <div class="mt-4 product-extra-info section-card">
                <h5 class="fw-semibold">Thông tin thêm</h5>
                <ul class="list-unstyled text-muted small mb-0">
                    <li>• Cam kết hàng chính hãng 100%</li>
                    <li>• Giao hàng toàn quốc</li>
                    <li>• Đổi trả trong 7 ngày nếu lỗi do nhà sản xuất</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- HÀNG 2: MÔ TẢ + THÀNH PHẦN + HDSD --}}
    <div class="row mt-5 g-4">
        <div class="col-lg-8">
            @if(!empty($product->description))
                <h4 class="fw-bold text-dark mb-3">Mô tả sản phẩm</h4>
                <div class="bg-white border rounded-3 p-4 product-content content-card h-100">
                    {!! $product->description !!}
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if(!empty($product->ingredients))
                <h4 class="fw-bold text-dark mb-3">Thành phần sản phẩm</h4>
                <div class="bg-light border rounded-3 p-4 product-content content-card mb-4">
                    {!! $product->ingredients !!}
                </div>
            @endif

            @if(!empty($product->usage_instructions))
                <h4 class="fw-bold text-dark mb-3">Hướng dẫn sử dụng</h4>
                <div class="bg-white border rounded-3 p-4 product-content content-card">
                    {!! $product->usage_instructions !!}
                </div>
            @endif
        </div>
    </div>

    {{-- HÀNG 3: ĐÁNH GIÁ --}}
    <div id="reviews" class="mt-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">Đánh giá ({{ $totalReviews }})</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="display-5 fw-bold text-orange">{{ $avgRating }}</span>
                    <div>
                        <div class="text-warning fs-5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">
                            {{ $totalReviews }} đánh giá cho sản phẩm này
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            @php
                $allActive = $ratingFilter ? '' : 'btn-warning text-white';
            @endphp

            <div class="btn-group me-3 flex-wrap">
                <a href="{{ route('product.show', [$product]) }}"
                   class="btn btn-sm {{ $allActive ?: 'btn-light' }}">
                    Tất cả ({{ $totalReviews }})
                </a>

                @for($star = 5; $star >= 1; $star--)
                    @php
                        $active = $ratingFilter == $star ? 'btn-warning text-white' : 'btn-light';
                    @endphp
                    <a href="{{ route('product.show', [$product, 'rating' => $star]) }}"
                       class="btn btn-sm {{ $active }}">
                        {{ $star }} ★ ({{ $ratingCounts[$star] ?? 0 }})
                    </a>
                @endfor
            </div>
        </div>

        <div class="border rounded-3 bg-white overflow-hidden review-box">
            @forelse($reviews as $review)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="fw-semibold">{{ $review->customer_name }}</div>
                        <small class="text-muted">
                            {{ $review->created_at->format('Y-m-d, H:i') }}
                        </small>
                    </div>

                    <div class="mb-1">
                        <span class="badge bg-danger me-2">{{ $review->rating }}★</span>
                        <span class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                    </div>

                    <div class="text-muted small mb-1">
                        {{ $product->name }}
                    </div>

                    <p class="mb-2">{{ $review->content }}</p>

                    @if($review->admin_reply)
                        <div class="mt-2 ps-3 border-start border-success">
                            <div class="badge bg-success mb-1">Shop</div>
                            <div class="small">{{ $review->admin_reply }}</div>
                            <small class="text-muted d-block mt-1">
                                {{ optional($review->replied_at ?? $review->updated_at)->format('Y-m-d, H:i') }}
                            </small>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-4 text-center text-muted">
                    Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $reviews->links() }}
        </div>

        <div id="write-review" class="mt-4">
            <h5 class="fw-bold mb-3">Viết đánh giá của bạn</h5>

            @auth
                <form action="{{ route('product.reviews.store', $product) }}" method="POST" class="border rounded-3 p-3 bg-white review-form">
                    @csrf

                   <div class="mb-3">
                        <label class="form-label fw-semibold d-block mb-2">Mức độ hài lòng</label>

                        <div class="rating-box">
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input
                                        type="radio"
                                        id="star{{ $i }}"
                                        name="rating"
                                        value="{{ $i }}"
                                        @checked(old('rating') == $i)
                                    >
                                    <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                                @endfor
                            </div>

                            <div class="rating-note" id="ratingNote">Chọn số sao đánh giá</div>
                        </div>

                        @error('rating')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nội dung đánh giá</label>
                        <textarea name="content" rows="4" class="form-control" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-warning text-white px-4">
                        Gửi đánh giá
                    </button>
                </form>
            @else
                <div class="alert alert-info">
                    Bạn cần <a href="{{ route('login.form') }}" class="fw-semibold">đăng nhập</a> để gửi đánh giá sản phẩm.
                </div>
            @endauth
        </div>
    </div>

    {{-- HÀNG 4: SẢN PHẨM LIÊN QUAN --}}
    @if(!empty($recentlyViewedProducts) && $recentlyViewedProducts->count())    
        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Sản phẩm đã xem gần đây</h4>
            </div>

            <div class="swiper relatedProductsSwiper">
                <div class="swiper-wrapper">
                   @foreach($recentlyViewedProducts as $item)
                        @php
                            $itemPrice = $item->price;
                            $itemIsHotdeal = (bool) $item->is_hotdeal;
                            $itemDiscountPercent = $item->discount_percent ?? null;

                            $itemCurrentPrice = $itemPrice;
                            if ($itemIsHotdeal && $itemDiscountPercent && $itemDiscountPercent > 0 && $itemPrice !== null) {
                                $itemCurrentPrice = floor($itemPrice * (100 - $itemDiscountPercent) / 100);
                            }

                            $itemImg = $item->image
                                ? asset('images/product/' . ltrim($item->image, '/'))
                                : asset('images/product/placeholder.jpg');
                        @endphp

                        <div class="swiper-slide">
                            <div class="card h-100 border-0 shadow-sm related-card">
                                <div class="position-relative">
                                    <a href="{{ route('product.show', $item->id) }}">
                                        <img src="{{ $itemImg }}"
                                             class="card-img-top"
                                             alt="{{ $item->name }}"
                                             style="height:220px; object-fit:cover;">
                                    </a>

                                    @if($itemIsHotdeal && $itemDiscountPercent && $itemDiscountPercent > 0)
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                            -{{ $itemDiscountPercent }}%
                                        </span>
                                    @endif
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title mb-2 related-name">
                                        <a href="{{ route('product.show', $item->id) }}"
                                           class="text-dark text-decoration-none">
                                            {{ $item->name }}
                                        </a>
                                    </h6>

                                    <div class="mt-auto">
                                        @if($itemIsHotdeal && $itemDiscountPercent && $itemDiscountPercent > 0 && $itemPrice !== null)
                                            <div class="mb-2">
                                                <span class="text-muted text-decoration-line-through small me-2">
                                                    {{ number_format($itemPrice, 0, ',', '.') }} đ
                                                </span>
                                                <span class="fw-bold text-danger">
                                                    {{ number_format($itemCurrentPrice, 0, ',', '.') }} đ
                                                </span>
                                            </div>
                                        @else
                                            <div class="fw-bold text-danger mb-2">
                                                {{ number_format($itemPrice, 0, ',', '.') }} đ
                                            </div>
                                        @endif

                                        <a href="{{ route('product.show', $item->id) }}"
                                           class="inline-flex w-full items-center justify-center rounded-2xl border !border-sky-500 !bg-white px-4 py-2.5 text-sm font-semibold !text-sky-600 shadow-sm transition-all duration-200 hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="swiper-button-prev related-swiper-prev"></div>
                <div class="swiper-button-next related-swiper-next"></div>
                <div class="swiper-pagination related-swiper-pagination"></div>
            </div>
        </div>
    @endif
</div>

@include('orders.buynow')
<script src="{{ asset('js/buynow.js') }}?v={{ file_exists(public_path('js/buynow.js')) ? filemtime(public_path('js/buynow.js')) : time() }}" defer></script>
<script src="{{ asset('js/detail.js') }}?v={{ file_exists(public_path('js/detail.js')) ? filemtime(public_path('js/detail.js')) : time() }}" defer></script>
@endsection
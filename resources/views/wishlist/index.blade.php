@extends('layout')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Sản phẩm yêu thích</h2>
            <p class="text-muted mb-0">Danh sách những sản phẩm bạn đã lưu.</p>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            ← Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if($wishlistItems->count())
        <div class="row g-4">
            @foreach($wishlistItems as $item)
                @php
                    $product = $item->product;

                    if (!$product) continue;

                    $placeholder = asset('images/product/placeholder.jpg');
                    $img = $product->image
                        ? asset('images/product/' . $product->image)
                        : $placeholder;

                    $listPrice = $product->price;
                    $isHotdeal = (bool) $product->is_hotdeal;
                    $discountPercent = $product->discount_percent ?? null;

                    $currentPrice = $listPrice;
                    if ($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null) {
                        $currentPrice = floor($listPrice * (100 - $discountPercent) / 100);
                    }

                    $stockQty = (int)($product->quantity ?? 0);
                @endphp

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden wishlist-card">
                        <div class="position-relative bg-white">
                            <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                <img src="{{ $img }}"
                                     alt="{{ $product->name }}"
                                     class="card-img-top"
                                     style="height:240px; object-fit:cover;">
                            </a>

                            @if($isHotdeal && $discountPercent && $discountPercent > 0)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-2">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif

                            <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-light border rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width:40px; height:40px;"
                                        title="Xóa khỏi yêu thích"
                                        onclick="return confirm('Bạn muốn xóa sản phẩm này khỏi danh sách yêu thích?')">
                                    <span class="text-danger" style="font-size:18px;">♥</span>
                                </button>
                            </form>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-semibold mb-2" style="min-height:48px;">
                                <a href="{{ route('product.show', $product->id) }}"
                                   class="text-dark text-decoration-none">
                                    {{ $product->name }}
                                </a>
                            </h6>

                            <div class="mb-2">
                                @if($isHotdeal && $discountPercent && $discountPercent > 0 && $listPrice !== null)
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="text-muted text-decoration-line-through small">
                                            {{ number_format($listPrice, 0, ',', '.') }} đ
                                        </span>
                                        <span class="fw-bold text-danger">
                                            {{ number_format($currentPrice, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                @else
                                    <div class="fw-bold text-danger">
                                        {{ $listPrice !== null ? number_format($listPrice, 0, ',', '.') . ' đ' : 'Liên hệ' }}
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                @if($stockQty > 0)
                                    <span class="badge bg-success-subtle text-success border">Còn hàng</span>
                                @else
                                    <span class="badge bg-secondary">Tạm hết hàng</span>
                                @endif
                            </div>

                            <div class="mt-auto d-grid gap-2">
                                <a href="{{ route('product.show', $product->id) }}"
                                    class="inline-flex w-full items-center justify-center rounded-2xl border !border-sky-500 !bg-white px-4 py-2.5 text-sm font-semibold !text-sky-600 shadow-sm transition-all duration-200 hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                    Xem chi tiết
                                </a>

                                @if($stockQty > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart d-flex gap-2 align-items-center flex-wrap">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-danger btn-sm w-100 rounded-3">
                                            Thêm vào giỏ
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm rounded-3" disabled>
                                        Tạm hết hàng
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $wishlistItems->links() }}
        </div>
    @else
        <div class="bg-white border rounded-4 shadow-sm p-5 text-center">
            <div style="font-size:56px; line-height:1;" class="mb-3 text-danger">♡</div>
            <h4 class="fw-bold mb-2">Chưa có sản phẩm yêu thích</h4>
            <p class="text-muted mb-4">
                Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.
            </p>
            <a href="{{ route('home') }}" class="btn btn-danger px-4 rounded-3">
                Tiếp tục mua sắm
            </a>
        </div>
    @endif
</div>
<script src="{{ asset('js/buynow.js') }}?v={{ filemtime(public_path('js/buynow.js')) }}" defer></script>
<script src="{{ asset('js/detail.js') }}?v={{ filemtime(public_path('js/detail.js')) }}" defer></script>
@endsection
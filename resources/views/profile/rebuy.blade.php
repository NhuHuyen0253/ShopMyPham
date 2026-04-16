@extends('layout')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                 
                    <ul class="list-group list-group-flush account-side-menu">
                        <li class="list-group-item {{ request()->routeIs('profile.info') ? 'active' : '' }}">
                            <a href="{{ route('profile.info') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-user"></i>
                                <span>Thông tin tài khoản</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-box"></i>
                                <span>Đơn hàng của tôi</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-heart"></i>
                                <span>Danh sách yêu thích</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.rebuy') ? 'active' : '' }}">
                            <a href="{{ route('profile.rebuy') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Mua lại</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.faq') ? 'active' : '' }}">
                            <a href="{{ route('profile.faq') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-circle-question"></i>
                                <span>Hỏi đáp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="p-4 p-md-5 border-bottom bg-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h3 class="fw-bold mb-1">Mua lại</h3>
                            <p class="text-muted mb-0">Các sản phẩm bạn đã từng mua trước đó.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5 bg-white">
                    @if($rebuyItems->count())
                        <div class="row g-3 g-md-4">
                            @foreach($rebuyItems as $item)
                                @php
                                    $product = $item->product;

                                    $img = $product && $product->image
                                        ? asset('images/product/' . ltrim($product->image, '/'))
                                        : asset('images/product/placeholder.jpg');
                                @endphp

                                @if($product)
                                    <div class="col-6 col-md-6 col-lg-4">
                                        <div class="card h-100 border-0 shadow-sm product-card rounded-4 overflow-hidden bg-white">
                                            <div class="position-relative">
                                                <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                                    <div class="ratio ratio-1x1 bg-light">
                                                        <img
                                                            src="{{ $img }}"
                                                            class="w-100 h-100 object-fit-cover"
                                                            alt="{{ $product->name }}">
                                                    </div>
                                                </a>
                                            </div>

                                            <div class="card-body d-flex flex-column text-center">
                                                <a href="{{ route('product.show', $product->id) }}"
                                                   class="text-decoration-none text-dark">
                                                    <h5 class="fw-bold mb-3 product-title">
                                                        {{ $product->name }}
                                                    </h5>
                                                </a>

                                                <div class="mb-2 text-danger fw-bold fs-5">
                                                    {{ number_format((int) $product->price, 0, ',', '.') }} đ
                                                </div>

                                            
                                                <div class="mt-auto d-grid gap-2">
                                                    <a href="{{ route('product.show', $product->id) }}"
                                                        class="inline-flex w-full items-center justify-center rounded-2xl border !border-sky-500 !bg-white px-4 py-2.5 text-sm font-semibold !text-sky-600 shadow-sm transition-all duration-200 hover:!border-pink-500 hover:!bg-pink-500 hover:!text-white no-underline hover:no-underline">
                                                        Xem chi tiết
                                                    </a>

                                                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">

                                                        <button type="submit" class="btn btn-danger btn-sm w-100 rounded-3 py-2 fw-semibold">
                                                            Mua lại
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $rebuyItems->links('vendor.pagination.custom') }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-3">Bạn chưa có sản phẩm nào để mua lại.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-pink rounded-pill px-4">
                                Mua sắm ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/detail.js') }}?v={{ file_exists(public_path('js/detail.js')) ? filemtime(public_path('js/detail.js')) : time() }}" defer></script>
@endsection
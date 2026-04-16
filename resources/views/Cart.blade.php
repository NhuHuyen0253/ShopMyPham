@extends('layout')

@section('title', 'Giỏ hàng')

@section('content')
@php
    $cart = $cart ?? [];

    if (!isset($totals)) {
        $subtotal = collect($cart)->sum(function ($product) {
            $listPrice = (int) ($product['price'] ?? 0);
            $isHotdeal = (bool) ($product['is_hotdeal'] ?? false);
            $discountPercent = (int) ($product['discount_percent'] ?? 0);
            $qty = max(1, (int) ($product['quantity'] ?? 1));

            $currentPrice = $listPrice;
            if ($isHotdeal && $discountPercent > 0 && $listPrice > 0) {
                $currentPrice = (int) floor($listPrice * (100 - $discountPercent) / 100);
            }

            return $currentPrice * $qty;
        });

        $discount = 0;
        $total = max(0, $subtotal - $discount);

        $totals = [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total'    => $total,
            'count'    => collect($cart)->sum(fn($p) => (int)($p['quantity'] ?? 0)),
        ];
    }
@endphp

<div class="container py-4 py-md-5 cart-page">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <h3 class="fw-bold mb-0">
            Giỏ hàng
            <span class="text-muted fs-6">({{ count($cart) }} sản phẩm)</span>
        </h3>

        <a href="{{ route('home') ?? url('/') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
        </a>
    </div>

    <div class="row g-4">
        {{-- CỘT TRÁI --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">

                    <div class="cart-select-all d-flex align-items-center mb-3">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2" />
                        <label for="selectAll" class="small text-muted mb-0">Chọn tất cả</label>
                        <span class="small text-muted ms-auto">Thao tác</span>
                    </div>

                    @forelse ($cart as $product)
                        @php
                            $img = $product['image_url'] ?? null;
                            $pid = (int) ($product['product_id'] ?? 0);

                            $listPrice = (int) ($product['price'] ?? 0);
                            $isHotdeal = (bool) ($product['is_hotdeal'] ?? false);
                            $discountPercent = (int) ($product['discount_percent'] ?? 0);

                            $currentPrice = $listPrice;
                            if ($isHotdeal && $discountPercent > 0 && $listPrice > 0) {
                                $currentPrice = (int) floor($listPrice * (100 - $discountPercent) / 100);
                            }

                            $qty = max(1, (int) ($product['quantity'] ?? 1));
                            $line = $currentPrice * $qty;
                        @endphp

                        <div class="cart-item"
                             data-cart-row
                             data-product-id="{{ $pid }}"
                             data-update-url="{{ route('cart.updateQty', ['id' => $pid]) }}"
                             data-remove-url="{{ route('cart.remove', ['id' => $pid]) }}">

                            <div class="cart-check">
                                <input type="checkbox" class="form-check-input js-item-check" data-product-id="{{ $pid }}" />
                            </div>

                            <div class="cart-thumb">
                                @if ($img)
                                    <img src="{{ $img }}"
                                         alt="{{ $product['name'] ?? 'Sản phẩm' }}"
                                         class="w-100 h-100 rounded-3"
                                         style="object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('d-none');">
                                    <div class="d-none bg-light rounded-3 w-100 h-100"></div>
                                @else
                                    <div class="bg-light rounded-3 w-100 h-100"></div>
                                @endif
                            </div>

                            <div class="cart-info">
                                <h6 class="mb-1 fw-semibold">{{ $product['name'] ?? 'Sản phẩm' }}</h6>

                                <div class="small text-muted mb-1">Đơn giá:</div>

                                @if($isHotdeal && $discountPercent > 0)
                                    <div>
                                        <span class="text-danger fw-bold">
                                            {{ number_format($currentPrice, 0, ',', '.') }} ₫
                                        </span>
                                        <span class="text-decoration-line-through text-muted ms-1">
                                            {{ number_format($listPrice, 0, ',', '.') }} ₫
                                        </span>
                                        <span class="badge bg-danger ms-1">-{{ $discountPercent }}%</span>
                                    </div>
                                @else
                                    <div class="text-danger fw-bold">
                                        {{ number_format($listPrice, 0, ',', '.') }} ₫
                                    </div>
                                @endif
                            </div>

                            <div class="cart-qty">
                                <div class="small text-muted mb-2">Số lượng</div>

                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="input-group input-group-sm qty-box">
                                        <button class="btn btn-outline-secondary js-qty-minus" type="button">−</button>
                                        <input type="number"
                                               min="1"
                                               class="form-control text-center js-qty-input"
                                               value="{{ $qty }}"
                                               data-product-id="{{ $pid }}">
                                        <button class="btn btn-outline-secondary js-qty-plus" type="button">+</button>
                                    </div>

                                    <button type="button" class="btn btn-pink btn-sm js-qty-update">
                                        Cập nhật
                                    </button>
                                </div>
                            </div>

                            <div class="cart-total text-end">
                                <div class="small text-muted mb-1">Thành tiền</div>
                                <div class="js-item-total text-danger fw-bold">
                                    {{ number_format($line, 0, ',', '.') }} ₫
                                </div>
                            </div>

                            <div class="cart-remove">
                                <button class="btn btn-link text-danger p-0 js-remove-from-cart" type="button">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-2 text-muted">Giỏ hàng của bạn đang trống.</div>
                            <a href="{{ route('home') ?? url('/') }}" class="btn btn-pink rounded-3 px-4">
                                Mua sắm ngay
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden summary-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>

                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <strong id="cart-subtotal">
                            {{ number_format((int) ($totals['subtotal'] ?? 0), 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Giảm giá</span>
                        <strong id="cart-discount">
                            {{ number_format((int) ($totals['discount'] ?? 0), 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    <hr>

                    <div class="summary-row total-row">
                        <span class="fw-bold">Tổng cộng</span>
                        <strong class="text-danger fs-5" id="cart-total">
                            {{ number_format((int) ($totals['total'] ?? 0), 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    <form method="POST" action="{{ route('cart.checkoutFromCart') }}" id="checkoutForm" class="mt-4">
                        @csrf
                        <input type="hidden" name="selected_ids" id="selectedProducts">

                        <button type="submit" class="btn btn-pink w-100 fw-bold rounded-3 py-2" {{ empty($cart) ? 'disabled' : '' }}>
                            Mua ngay
                        </button>
                    </form>

                    <div class="alert alert-light border rounded-4 mt-4 mb-0 small text-muted">
                        Vui lòng chọn sản phẩm muốn thanh toán trước khi bấm <strong>Mua ngay</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-pink {
        background: #e83e8c;
        color: #fff;
        border: none;
    }

    .btn-pink:hover {
        background: #d63384;
        color: #fff;
    }

    .cart-select-all {
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f1f1;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 24px 90px 1fr 220px 140px 50px;
        gap: 14px;
        align-items: center;
        padding: 18px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .cart-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .cart-thumb {
        width: 90px;
        height: 90px;
        flex: 0 0 90px;
    }

    .cart-info h6 {
        line-height: 1.5;
    }

    .qty-box {
        width: 130px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        color: #444;
    }

    .total-row {
        margin-bottom: 0;
        align-items: center;
    }

    .summary-card {
        position: sticky;
        top: 20px;
    }

    @media (max-width: 991.98px) {
        .summary-card {
            position: static;
        }

        .cart-item {
            grid-template-columns: 24px 80px 1fr;
            gap: 12px;
        }

        .cart-qty,
        .cart-total,
        .cart-remove {
            grid-column: 3 / 4;
        }

        .cart-total {
            text-align: left !important;
        }
    }

    @media (max-width: 575.98px) {
        .cart-item {
            grid-template-columns: 24px 70px 1fr;
        }

        .cart-thumb {
            width: 70px;
            height: 70px;
        }

        .qty-box {
            width: 120px;
        }
    }
</style>
@endsection
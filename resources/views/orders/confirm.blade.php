@extends('layout')

@section('title', 'Thanh toán giỏ hàng')

@section('content')
<div class="container py-4 py-md-5 checkout-page">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <h3 class="fw-bold mb-0">Thanh toán giỏ hàng</h3>
        <a href="{{ route('cart.view') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="custom-success-alert mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $subtotal = 0;
    @endphp

    <div class="row g-4">
        {{-- Form thanh toán --}}
        <div class="col-12 col-lg-7">
            <form method="POST" action="{{ route('order.checkout') }}" id="checkoutForm">
                @csrf

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4">Thông tin nhận hàng</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Họ tên</label>
                            <input
                                type="text"
                                class="form-control rounded-3"
                                name="fullname"
                                placeholder="Nhập họ tên người nhận"
                                required
                                value="{{ old('fullname', auth()->user()->name ?? '') }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input
                                type="text"
                                class="form-control rounded-3"
                                name="phone"
                                placeholder="Nhập số điện thoại"
                                required
                                value="{{ old('phone') }}"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Địa chỉ nhận hàng</label>
                            <textarea
                                class="form-control rounded-3"
                                name="address"
                                rows="3"
                                placeholder="Nhập địa chỉ nhận hàng"
                                required
                            >{{ old('address') }}</textarea>
                        </div>

                        <div class="checkout-box bg-light-pink mb-4">
                            <h6 class="fw-bold mb-3">Phương thức thanh toán</h6>

                            <label class="payment-option mb-2">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="cod" checked>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </label>

                            <label class="payment-option">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="vnpay">
                                <span>Thanh toán trực tuyến qua VNPay</span>
                            </label>
                        </div>

                        <div class="checkout-box mb-4">
                            <h6 class="fw-bold mb-3">Thông tin vận chuyển</h6>

                            <div class="info-row">
                                <span>Đơn vị vận chuyển</span>
                                <strong id="shipping_provider">GHN</strong>
                            </div>

                            <div class="info-row">
                                <span>Hình thức</span>
                                <strong id="shipping_service">Giao hàng tiêu chuẩn</strong>
                            </div>

                            <div class="info-row mb-0">
                                <span>Phí vận chuyển</span>
                                <strong id="shipping_fee_text" class="text-danger">
                                    {{ number_format($defaultShippingFee ?? 30000, 0, ',', '.') }} đ
                                </strong>
                            </div>
                        </div>

                        <input type="hidden" name="shipping_fee" id="shipping_fee" value="{{ $defaultShippingFee ?? 30000 }}">
                        <input type="hidden" name="shipping_provider" value="GHN">
                        <input type="hidden" name="shipping_service" value="Giao hàng tiêu chuẩn">

                        <button type="submit" class="btn btn-pink rounded-3 px-4 py-2 w-100 fw-semibold">
                            Xác nhận đặt hàng
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tóm tắt đơn hàng --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden order-summary-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Đơn hàng của bạn</h5>

                    @foreach($items as $it)
                        @php
                            $product = $it['product'];
                            $qty = (int) ($it['qty'] ?? 1);

                            $listPrice = (int) ($product->price ?? 0);
                            $isHotdeal = (bool) ($product->is_hotdeal ?? false);
                            $discountPercent = (int) ($product->discount_percent ?? 0);

                            $currentPrice = $listPrice;
                            if ($isHotdeal && $discountPercent > 0 && $listPrice > 0) {
                                $currentPrice = (int) floor($listPrice * (100 - $discountPercent) / 100);
                            }

                            $line = $currentPrice * $qty;
                            $subtotal += $line;
                        @endphp

                        <div class="order-item">
                            <div class="flex-grow-1 pe-3">
                                <div class="fw-semibold mb-1">{{ $product->name }}</div>
                                <small class="text-muted d-block mb-1">Số lượng: {{ $qty }}</small>

                                @if($isHotdeal && $discountPercent > 0)
                                    <small class="text-muted">
                                        <span class="text-danger fw-bold">
                                            {{ number_format($currentPrice, 0, ',', '.') }} đ
                                        </span>
                                        <span class="text-decoration-line-through ms-1">
                                            {{ number_format($listPrice, 0, ',', '.') }} đ
                                        </span>
                                        <span class="badge bg-danger ms-1">-{{ $discountPercent }}%</span>
                                    </small>
                                @else
                                    <small class="text-muted">
                                        {{ number_format($listPrice, 0, ',', '.') }} đ
                                    </small>
                                @endif
                            </div>

                            <div class="fw-semibold text-nowrap">
                                {{ number_format($line, 0, ',', '.') }} đ
                            </div>
                        </div>
                    @endforeach

                    <div class="summary-row mt-4">
                        <span>Tạm tính</span>
                        <span id="subtotal_text">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span id="shipping_fee_summary">
                            {{ number_format($defaultShippingFee ?? 30000, 0, ',', '.') }} đ
                        </span>
                    </div>

                    <hr>

                    <div class="summary-row total-row">
                        <strong>Tổng thanh toán</strong>
                        <strong class="text-danger fs-5" id="grand_total_text">
                            {{ number_format($subtotal + ($defaultShippingFee ?? 30000), 0, ',', '.') }} đ
                        </strong>
                    </div>

                    <div class="alert alert-info rounded-4 mt-4 mb-0 small">
                        Vui lòng kiểm tra kỹ họ tên, số điện thoại, địa chỉ nhận hàng và phương thức thanh toán trước khi đặt đơn.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const subtotal = {{ (int) $subtotal }};
    const defaultShippingFee = {{ (int) ($defaultShippingFee ?? 30000) }};

    const checkoutForm = document.getElementById('checkoutForm');
    const shippingFeeInput = document.getElementById('shipping_fee');
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const shippingFeeSummary = document.getElementById('shipping_fee_summary');
    const grandTotalText = document.getElementById('grand_total_text');

    function formatVND(number) {
        return new Intl.NumberFormat('vi-VN').format(number) + ' đ';
    }

    function updateTotal(shippingFee) {
        shippingFee = parseInt(shippingFee) || 0;
        shippingFeeInput.value = shippingFee;
        shippingFeeText.innerText = formatVND(shippingFee);
        shippingFeeSummary.innerText = formatVND(shippingFee);
        grandTotalText.innerText = formatVND(subtotal + shippingFee);
    }

    checkoutForm.addEventListener('submit', function (e) {
        const address = document.querySelector('[name="address"]').value.trim();

        if (!address) {
            e.preventDefault();
            alert('Vui lòng nhập địa chỉ nhận hàng.');
            return;
        }

        if (!shippingFeeInput.value || parseInt(shippingFeeInput.value) < 0) {
            updateTotal(defaultShippingFee);
        }
    });

    updateTotal(defaultShippingFee);
</script>
@endpush
@endsection

@section('styles')
<style>
    .checkout-page .form-control {
        min-height: 46px;
    }

    .checkout-page textarea.form-control {
        min-height: unset;
    }

    .btn-pink {
        background: #e83e8c;
        color: #fff;
        border: none;
    }

    .btn-pink:hover {
        background: #d63384;
        color: #fff;
    }

    .custom-success-alert {
        background: #fff0f6;
        color: #d63384;
        border: 1px solid #f5c2da;
        padding: 14px 16px;
        border-radius: 16px;
        font-weight: 500;
        box-shadow: 0 0.25rem 0.75rem rgba(232, 62, 140, 0.08);
    }

    .bg-light-pink {
        background: #fff7fb;
    }

    .checkout-box {
        border: 1px solid #f1e5ec;
        border-radius: 16px;
        padding: 16px;
        background: #fff;
    }

    .payment-option {
        display: flex;
        align-items: center;
        padding: 10px 0;
        cursor: pointer;
    }

    .info-row,
    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .summary-row {
        color: #444;
    }

    .total-row {
        align-items: center;
        margin-bottom: 0;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .order-item:first-of-type {
        padding-top: 0;
    }

    .order-summary-card {
        position: sticky;
        top: 20px;
    }

    @media (max-width: 991.98px) {
        .order-summary-card {
            position: static;
        }
    }
</style>
@endsection
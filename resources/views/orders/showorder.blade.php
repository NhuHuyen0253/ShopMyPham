@extends('layout')

@section('content')
@php
    $fmt = fn($n) => number_format((int) $n, 0, ',', '.') . ' đ';

    $fallbackSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='72' height='72'><rect width='100%' height='100%' fill='%23f3f4f6'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%239ca3af' font-size='10'>no image</text></svg>";

    $status = (string) ($order->status ?? 'pending');

    $statusUI = [
        'pending'          => ['label' => 'Chờ xác nhận',        'color' => 'warning',   'icon' => '⏳'],
        'awaiting_payment' => ['label' => 'Chờ thanh toán',      'color' => 'warning',   'icon' => '⏳'],
        'processing'       => ['label' => 'Đang xử lý',          'color' => 'info',      'icon' => '⚙️'],
        'shipped'          => ['label' => 'Đã gửi hàng',         'color' => 'primary',   'icon' => '🚚'],
        'completed'        => ['label' => 'Hoàn tất',            'color' => 'success',   'icon' => '✅'],
        'paid'             => ['label' => 'Đã thanh toán',       'color' => 'success',   'icon' => '✅'],
        'cancelled'        => ['label' => 'Đã huỷ',              'color' => 'secondary', 'icon' => '✖️'],
        'payment_failed'   => ['label' => 'Thanh toán thất bại', 'color' => 'danger',    'icon' => '❌'],
        'failed'           => ['label' => 'Thất bại',            'color' => 'danger',    'icon' => '❌'],
    ];

    $ui = $statusUI[$status] ?? $statusUI['pending'];

    $paidBadge = ((int) ($order->is_paid ?? 0) === 1)
        ? ['label' => 'Đã thanh toán', 'color' => 'success']
        : ['label' => 'Chưa thanh toán', 'color' => 'secondary'];

    $pm = (string) ($order->payment_method ?? 'cod');
    $pmLabel = match ($pm) {
        'vnpay' => 'VNPay',
        'cod'   => 'Thanh toán khi nhận hàng (COD)',
        'bank'  => 'Chuyển khoản ngân hàng',
        default => strtoupper($pm),
    };

    $subtotal    = (int) $order->orderItems->sum(fn($it) => (int) $it->price * (int) $it->quantity);
    $discount    = (int) ($order->discount ?? 0);
    $shippingFee = (int) ($order->shipping_fee ?? 0);
    $grand       = max(0, $subtotal - $discount + $shippingFee);

    $steps = [
        ['key' => 'pending',          'label' => 'Đặt hàng thành công'],
        ['key' => 'awaiting_payment', 'label' => 'Chờ thanh toán'],
        ['key' => 'processing',       'label' => 'Đang xử lý'],
        ['key' => 'shipped',          'label' => 'Đã gửi hàng'],
        ['key' => 'completed',        'label' => 'Hoàn tất'],
    ];

    $progressRank = [
        'pending'          => 1,
        'awaiting_payment' => 2,
        'processing'       => 3,
        'shipped'          => 4,
        'completed'        => 5,
        'paid'             => 3,
    ];

    $rank = $progressRank[$status] ?? 1;
    if ((int) ($order->is_paid ?? 0) === 1 && $rank < 3) {
        $rank = 3;
    }

    $cannotCancelStatuses = ['shipped', 'completed', 'cancelled'];
    $canCancel = !in_array($status, $cannotCancelStatuses, true);

    $canShowRefund = ($status === 'cancelled' && (int) ($order->is_paid ?? 0) === 1);
    $isRefunded    = (int) ($order->is_refunded ?? 0) === 1;

    $refundBadge = null;
    if ($canShowRefund) {
        $refundBadge = $isRefunded
            ? ['label' => '💸 Đã hoàn tiền', 'color' => 'success']
            : ['label' => '💸 Chờ hoàn tiền', 'color' => 'warning'];
    }

    $firstItem = $order->orderItems->first();
@endphp

<div class="container py-4 py-md-5">
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">Chi tiết đơn hàng</h2>
                <p class="text-muted mb-0">Theo dõi trạng thái và thông tin đầy đủ của đơn hàng.</p>
            </div>
            <a href="{{ route('profile.orders') }}" class="btn btn-outline-secondary rounded-pill px-4">
                ← Quay lại đơn hàng của tôi
            </a>
        </div>
    </div>

    {{-- Header đơn hàng --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4">
                <div>
                    <h4 class="fw-bold mb-2">Đơn hàng #{{ $order->id }}</h4>
                    <div class="text-muted d-flex flex-wrap align-items-center gap-2">
                        <span>Ngày tạo: <strong>{{ $order->created_at?->format('d/m/Y H:i') }}</strong></span>
                        <button type="button" class="btn btn-sm btn-light border" onclick="copyText('{{ $order->id }}')">
                            Copy mã đơn
                        </button>
                    </div>
                </div>

                <div class="text-lg-end">
                    <span class="badge bg-{{ $ui['color'] }} px-3 py-2 fs-6 rounded-pill">
                        {{ $ui['icon'] }} {{ $ui['label'] }}
                    </span>

                    <div class="mt-2 d-flex flex-column align-items-lg-end gap-2">
                        <span class="badge bg-{{ $paidBadge['color'] }} px-3 py-2 rounded-pill">
                            {{ $paidBadge['label'] }} @if($pm === 'vnpay') • VNPay @endif
                        </span>

                        @if($refundBadge)
                            <span class="badge bg-{{ $refundBadge['color'] }} {{ $refundBadge['color'] === 'warning' ? 'text-dark' : '' }} px-3 py-2 rounded-pill">
                                {{ $refundBadge['label'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Progress --}}
            <div class="mt-4">
                <div class="fw-semibold text-muted mb-3">Tiến trình đơn hàng</div>
                <div class="row g-3">
                  @foreach($steps as $s)
                      @php
                          $r = $progressRank[$s['key']] ?? 1;
                          $active = $r <= $rank;
                      @endphp
                      <div class="col-12 col-md">
                          <div class="border rounded-4 p-3 h-100 {{ $active ? 'bg-warning-subtle border-warning shadow-sm' : 'bg-white border-light' }}">
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span
                                      class="rounded-circle {{ $active ? 'bg-warning' : 'bg-secondary' }}"
                                      style="width:12px; height:12px; display:inline-block;">
                                  </span>

                                  <span class="fw-bold {{ $active ? 'text-warning-emphasis' : 'text-dark' }}">
                                      {{ $s['label'] }}
                                  </span>
                              </div>

                              <div class="small {{ $active ? 'text-warning-emphasis' : 'text-muted' }}">
                                  {{ $active ? 'Đã cập nhật' : 'Chưa tới' }}
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Cột trái --}}
        <div class="col-lg-8">

            {{-- Địa chỉ nhận hàng --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Địa chỉ nhận hàng</h5>
                    <div class="fw-semibold">{{ $order->receiver_name ?? 'Khách hàng' }}</div>
                    <div class="text-muted">{{ $order->receiver_phone }}</div>
                    <div class="mt-2">{{ $order->receiver_addr }}</div>
                </div>
            </div>

            {{-- Vận chuyển --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin vận chuyển</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Đơn vị vận chuyển</span>
                        <strong>{{ $order->shipping_carrier ?: 'Chưa có' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mã vận đơn</span>
                        <strong>{{ $order->tracking_code ?: 'Chưa có' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển</span>
                        <strong>{{ $fmt($shippingFee) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ngày gửi hàng</span>
                        <strong>{{ $order->shipped_at ? optional($order->shipped_at)->format('d/m/Y H:i') : 'Chưa có' }}</strong>
                    </div>

                    @if(!empty($order->shipping_note))
                        <div class="mt-3">
                            <div class="small text-muted">Ghi chú vận chuyển</div>
                            <div>{{ $order->shipping_note }}</div>
                        </div>
                    @endif

                    <div class="small text-muted mt-3">
                        * Khi shop tạo vận đơn, thông tin sẽ được cập nhật tại đây.
                    </div>
                </div>
            </div>

            {{-- Sản phẩm --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Sản phẩm</h5>
                        <div class="text-muted small">
                            Tổng:
                            <span class="fw-bold text-danger">{{ $fmt($grand) }}</span>
                        </div>
                    </div>
                </div>

                @if($order->orderItems->isEmpty())
                    <div class="p-4 text-center text-muted">
                        Đơn hàng không có sản phẩm.
                    </div>
                @else
                    @foreach($order->orderItems as $item)
                        @php
                            $p    = $item->product;
                            $img  = $p?->image ? asset('images/product/' . ltrim($p->image, '/')) : $fallbackSvg;
                            $name = $p?->name ?? 'Sản phẩm đã xoá';
                            $unit = (int) $item->price;
                            $qty  = (int) $item->quantity;
                            $line = $unit * $qty;
                        @endphp

                        <div class="p-4 border-bottom">
                            <div class="d-flex gap-3">
                                <img
                                    src="{{ $img }}"
                                    alt="product"
                                    class="rounded-3 border"
                                    style="width: 78px; height: 78px; object-fit: cover;"
                                >

                                <div class="flex-fill">
                                    <div class="fw-bold">{{ html_entity_decode($name) }}</div>
                                    <div class="small text-muted mt-1">Đơn giá: <strong>{{ $fmt($unit) }}</strong></div>
                                    <div class="small text-muted">Số lượng: <strong>x{{ $qty }}</strong></div>
                                </div>

                                <div class="text-end" style="min-width: 140px;">
                                    <div class="small text-muted">Thành tiền</div>
                                    <div class="fw-bold text-danger fs-6">{{ $fmt($line) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Thanh toán --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>

                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        {{ $pmLabel }}
                    </span>

                    @if($pm === 'vnpay')
                        <div class="small text-muted mt-2">
                            * Đơn VNPay sẽ tự cập nhật trạng thái thanh toán.
                        </div>
                    @endif

                    @if($canShowRefund && !empty($order->refund_note))
                        <div class="small text-muted mt-2">
                            Ghi chú hoàn tiền: {{ $order->refund_note }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cột phải --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 18px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tóm tắt thanh toán</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính</span>
                        <strong>{{ $fmt($subtotal) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giảm giá</span>
                        <strong class="text-success">- {{ $fmt($discount) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển</span>
                        <strong>{{ $fmt($shippingFee) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Tổng thanh toán</span>
                        <span class="fw-bold text-danger fs-4">{{ $fmt($grand) }}</span>
                    </div>

                    <div class="small text-muted mt-2">
                        @if((int)($order->is_paid ?? 0) === 1)
                            Bạn đã thanh toán đơn hàng này.
                        @else
                            @if($pm === 'vnpay')
                                Đang chờ xác nhận thanh toán (VNPay).
                            @else
                                Đơn COD sẽ thanh toán khi nhận hàng.
                            @endif
                        @endif
                    </div>

                    @if($refundBadge)
                        <div class="small text-muted mt-2">
                            Trạng thái hoàn tiền:
                            <span class="fw-semibold">
                                @if((int)($order->is_refunded ?? 0) === 1)
                                    Đã hoàn tiền
                                @else
                                    Chờ hoàn tiền
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ url('/') }}" class="btn btn-light border rounded-pill">
                            Tiếp tục mua sắm
                        </a>

                        @if($firstItem?->product?->id)
                            <button
                                type="button"
                                class="btn btn-outline-success rounded-pill"
                                id="btn-rebuy"
                                data-product-id="{{ $firstItem->product->id }}"
                                data-qty="{{ (int) ($firstItem->quantity ?? 1) }}">
                                Mua lại (thêm vào giỏ)
                            </button>
                        @endif

                        <a href="{{ url('/contact') }}" class="btn btn-primary rounded-pill">
                            Liên hệ hỗ trợ
                        </a>

                        @if($canCancel)
                            <form method="POST"
                                action="{{ route('order.cancel', ['id' => $order->id]) }}"
                                onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn #{{ $order->id }}?');">
                                @csrf
                                @method('PATCH')

                                <div class="mb-2 text-start">
                                    <label class="form-label fw-semibold small">Lý do hủy đơn</label>
                                    <textarea
                                        name="cancel_reason"
                                        class="form-control rounded-4"
                                        rows="3"
                                        placeholder="Ví dụ: Tôi đặt nhầm sản phẩm / muốn đổi địa chỉ / không còn nhu cầu..."
                                        required>{{ old('cancel_reason') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                                    Hủy đơn
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary rounded-pill" disabled>
                                Không thể hủy (đơn đã gửi/hoàn tất)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã copy: ' + text);
    }).catch(() => {
        alert('Không thể copy mã đơn.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-rebuy');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const productId = btn.dataset.productId;
        const qty = btn.dataset.qty || 1;

        btn.disabled = true;
        const oldText = btn.innerText;
        btn.innerText = 'Đang thêm...';

        try {
            const res = await fetch(`{{ route('cart.add') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: parseInt(productId),
                    quantity: parseInt(qty),
                })
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data?.message || 'Thêm vào giỏ hàng thất bại.');
                return;
            }

            const badge = document.getElementById('cartCount');
            if (badge && typeof data.count !== 'undefined') {
                badge.textContent = data.count;
                badge.dataset.count = data.count;
                if (Number(data.count) > 0) {
                    badge.classList.remove('is-empty');
                }
            }

            alert(data.message || 'Đã thêm vào giỏ hàng ✅');
        } catch (e) {
            alert('Có lỗi mạng, thử lại nhé.');
        } finally {
            btn.disabled = false;
            btn.innerText = oldText;
        }
    });
});
</script>
@endsection
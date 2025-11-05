@extends('layout')

@section('content')
<div class="container my-4">
  <div class="row g-3 align-items-start">
    {{-- Cột trái: danh sách sản phẩm --}}
    <div class="col-md-8">
      <h4 class="mb-2">
        Giỏ hàng <span class="text-muted">({{ count(session('cart', [])) }} sản phẩm)</span>
      </h4>

      <div class="card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center mb-2 text-muted" style="gap:12px">
          <input type="checkbox" id="selectAll" />
          <small class="me-3">Chọn tất cả</small>
          <small class="ms-auto">Số lượng</small>
          <small style="width:90px" class="text-end">Thao tác</small>
        </div>

        @foreach (session('cart', []) as $product)
          @php $img = $product['image_url'] ?? null; @endphp

          <div class="d-flex border-top py-3 align-items-center" data-cart-row data-product-id="{{ $product['product_id'] }}" style="gap:12px">
            <input type="checkbox" class="js-item-check" data-product-id="{{ $product['product_id'] }}"/>

            <div class="me-2">
              @if ($img)
                <img src="{{ $img }}" alt="{{ $product['name'] }} "
                     width="72" height="72" class="rounded" style="object-fit:cover;display:inline-block"
                     onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                <div class="hidden" style="width:72px;height:72px;background:#f3f4f6;border-radius:8px;"></div>
              @else
                <div style="width:72px;height:72px;background:#f3f4f6;border-radius:8px;"></div>
              @endif
            </div>

            <div class="flex-grow-1">
              <h6 class="mb-1">{{ $product['name'] }}</h6>
              <strong class="text-danger">{{ number_format($product['price'], 0, ',', '.') }} ₫</strong>
            </div>

            <div class="input-group input-group-sm" style="width:50px">
              <input type="number" min="1" class="form-control text-center js-qty-input"
                     value="{{ $product['quantity'] }}" data-product-id="{{ $product['product_id'] }}">
            </div>

            <div style="width:90px" class="text-end">
              <button class="btn btn-link text-danger js-remove-from-cart"
                      data-action="{{ route('cart.remove') }}"
                      data-product-id="{{ $product['product_id'] }}">
                Xóa
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Cột phải: Hóa đơn --}}
    <div class="col-md-4">
      <div class="card border-0 shadow-sm p-3 position-sticky" style="top: 1rem;">
        <h5 class="mb-3">Hóa đơn của bạn</h5>
        @php
          $cart = session('cart', []);
          $subtotal = array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $cart));
        @endphp
        <div class="d-flex justify-content-between mb-2">
          <span>Tạm tính:</span>
          <strong class="js-subtotal">{{ number_format($subtotal, 0, ',', '.') }} ₫</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span>Giảm giá:</span>
          <strong>0 ₫</strong>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-3">
          <span><strong>Tổng cộng:</strong></span>
          <strong class="text-danger fs-5 js-grand">{{ number_format($subtotal, 0, ',', '.') }} ₫</strong>
        </div>

        <button class="btn btn-warning w-100 fw-bold">Tiến hành đặt hàng</button>
      </div>
    </div>
  </div>

  {{-- Nút tiếp tục mua sắm --}}
  <div class="mt-3">
    <a href="{{ route('home') ?? url('/') }}" class="btn btn-outline-secondary">
      &larr; Tiếp tục mua sắm
    </a>
  </div>
</div>
@endsection
@push('scripts')
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const updateQtyUrl = @json(route('cart.updateQty')); // route đã tồn tại trong Controller

  let timer;
  document.addEventListener('input', function(e) {
    const el = e.target;
    if (!el.classList.contains('js-qty-input')) return;

    clearTimeout(timer);
    timer = setTimeout(() => {
      const productId = el.dataset.productId;
      const quantity  = Math.max(1, parseInt(el.value || '1', 10));

      fetch(updateQtyUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity })
      })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;

        const row = el.closest('[data-cart-row]');
        const line = row?.querySelector('.js-item-total');
        if (line) line.textContent = data.item_total;

        const sub = document.querySelector('.js-subtotal');
        if (sub) sub.textContent = data.subtotal;

        const grand = document.querySelector('.js-grand');
        if (grand) grand.textContent = data.grand;
      })
      .catch(console.error);
    }, 250);
  });
</script>
@endpush




<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeBNModal()"></button>
      </div>

      <div class="modal-body">
        <form id="bn-form" method="POST" action="{{ $buyNowAction ?? route('order.buynow') }}">
          @csrf

          <div class="d-flex align-items-center gap-3">
            @php
               $placeholder = asset('images/product/placeholder.jpg');

              $mainImg = $product->image
                  ? asset('images/product/' . $product->image)
                  : $placeholder;
            @endphp

            <div class="border rounded-3 overflow-hidden bg-white">
              <img id="mainImg"
                  src="{{ $mainImg }}"
                  alt="Sản phẩm"
                  class="w-100"
                  style="max-height:420px; object-fit:contain;">
            </div>

            <div class="flex-fill">
              <div id="bn-name" class="fw-semibold mb-1">Tên sản phẩm</div>

              <div class="row g-2 align-items-center">
                <div class="col-auto">
                  <div class="text-muted small">Giá / 1 sản phẩm</div>
                  <div id="bn-price" class="text-danger fw-bold">0 đ</div>
                </div>

                <div class="col-auto">
                  <div class="text-muted small">Số lượng</div>
                  <div class="input-group" style="width:130px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">−</button>
                    <input id="bn-qty" name="quantity" type="number" class="form-control text-center"
                           min="1" step="1" value="1" inputmode="numeric" pattern="[0-9]*">
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">+</button>
                  </div>
                </div>

                <div class="col text-end">
                  <div class="text-muted small">Tổng tiền</div>
                  <div id="bn-total" class="text-danger fw-bold">0 đ</div>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" id="bn-product-id" name="product_id" value="">
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="closeBNModal()">Đóng</button>
        <button type="submit" form="bn-form" class="btn btn-primary">Xác nhận mua</button>
      </div>
    </div>
  </div>
</div>

<style>
  #buyNowModal.show:not(.fade) { display:flex !important; }
  #buyNowModal[aria-modal="true"] .modal-dialog { margin:auto; }
</style>
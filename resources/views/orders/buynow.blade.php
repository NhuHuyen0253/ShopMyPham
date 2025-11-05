<!-- Buy Now Modal (Bootstrap) -->
<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true" aria-labelledby="buyNowTitle">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-3">
      <div class="modal-header">
        <h5 class="modal-title" id="buyNowTitle">
          <span id="bn-name">Sản phẩm</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <form id="buyNowForm" method="POST" action="{{ route('order.buynow') }}">
        @csrf
        <input type="hidden" name="product_id" id="bn-product-id">

        <div class="modal-body">
          <div class="d-flex gap-3 align-items-start">
            <img id="bn-image" src="" alt="" class="rounded" style="width:72px;height:72px;object-fit:cover">
            <div class="flex-grow-1">
              <div class="small text-muted">Giá / 1 sản phẩm</div>
              <div id="bn-price" class="fs-5 fw-semibold text-danger">0 đ</div>
            </div>
          </div>

          <hr class="my-3">

          <div class="row g-3 align-items-center">
            <div class="col-auto">
              <label for="bn-qty" class="col-form-label mb-0">Số lượng</label>
            </div>
            <div class="col-auto">
              <div class="input-group" style="width:160px">
                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQty('bn-qty')">−</button>
                <input
                  type="number"
                  name="quantity"
                  id="bn-qty"
                  class="form-control text-center"
                  value="1"
                  min="1"
                  step="1"
                  inputmode="numeric">
                <button type="button" class="btn btn-outline-secondary" onclick="increaseQty('bn-qty')">+</button>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="fw-medium">Tổng tiền</div>
            <div id="bn-total" class="fs-5 fw-bold text-danger">0 đ</div>
          </div>
        </div>

    </div>
  </div>
</div>

// buynow.js
// Helpers cho modal (xem nhanh / mua ngay)
(function () {
  'use strict';

  function setupModal(modalEl, { productId, name, price, quantity = 1 }) {
    if (!modalEl) return;
    const $ = (sel) => modalEl.querySelector(sel);

    const productIdInput = $('input[name="product_id"]');
    const nameInput      = $('input[name="product_name"]');
    const priceInput     = $('input[name="price"]');
    const qtyInput       = $('input[name="quantity"]');

    if (productIdInput) productIdInput.value = productId;
    if (nameInput)      nameInput.value      = name;
    if (priceInput)     priceInput.value     = price;
    if (qtyInput)       qtyInput.value       = quantity;
  }

  function openModalWithProduct(id, name, price, image) {
    const modal = document.getElementById('productModal');
    if (!modal) return;

    setupModal(modal, { productId: id, name, price, quantity: 1 });

    const imgEl   = modal.querySelector('#modalImage');
    const nameEl  = modal.querySelector('#modalName');
    const priceEl = modal.querySelector('#modalPrice');

    if (imgEl)   imgEl.src = image;
    if (nameEl)  nameEl.textContent = name;
    if (priceEl) priceEl.textContent = Number(price).toLocaleString('vi-VN') + ' đ';

    const addBtn = document.getElementById('addToCartBtn');
    const formBN = document.getElementById('buyNowForm');
    if (addBtn) { addBtn.style.display = 'block'; addBtn.dataset.id = id; }
    if (formBN) formBN.style.display = 'none';

    modal.style.display = 'flex';
  }

  function openBuyNowModal(id, name, price, image) {
    const modal = document.getElementById('buynowModal');
    if (!modal) return;

    const qtyFromProductModal = parseInt(document.getElementById('quantity')?.value || '1', 10) || 1;

    setupModal(modal, { productId: id, name, price, quantity: qtyFromProductModal });

    const imgEl   = modal.querySelector('#buynowImage');
    const nameEl  = modal.querySelector('#buynowName');
    const priceEl = modal.querySelector('#buynowPrice');
    const totalEl = modal.querySelector('#buynowTotal');
    const qtyEl   = modal.querySelector('input[name="quantity"]');

    if (imgEl)   imgEl.src = image;
    if (nameEl)  nameEl.textContent = name;
    if (priceEl) priceEl.textContent = Number(price).toLocaleString('vi-VN') + ' đ';

    const updateTotal = () => {
      const q = parseInt(qtyEl?.value || '1', 10) || 1;
      if (totalEl) totalEl.textContent = 'Thành tiền: ' + (Number(price) * q).toLocaleString('vi-VN') + ' đ';
    };
    qtyEl?.addEventListener('input', updateTotal);
    updateTotal();

    const productModal = document.getElementById('productModal');
    if (productModal) productModal.style.display = 'none';
    modal.style.display = 'flex';
  }

  function increaseQty(inputId = 'quantity') {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.value = parseInt(input.value || '1', 10) + 1;
    input.dispatchEvent(new Event('input'));
  }

  function decreaseQty(inputId = 'quantity') {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.value = Math.max(1, parseInt(input.value || '1', 10) - 1);
    input.dispatchEvent(new Event('input'));
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
  }

  // Expose cho HTML nếu cần gọi trực tiếp
  window.openModalWithProduct = openModalWithProduct;
  window.openBuyNowModal      = openBuyNowModal;
  window.increaseQty          = increaseQty;
  window.decreaseQty          = decreaseQty;
  window.closeModal           = closeModal;
})();

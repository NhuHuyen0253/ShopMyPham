(function () {
  'use strict';

  const FALLBACK_IMG =
    "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='72' height='72'><rect width='100%' height='100%' fill='%23f3f4f6'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%239ca3af' font-size='10'>no image</text></svg>";

  function toNumberPrice(p) {
    if (typeof p === 'number') return p;
    if (typeof p === 'string') {
      const cleaned = p.replace(/[^\d]/g, '');
      const n = Number(cleaned);
      return Number.isFinite(n) ? n : 0;
    }
    return 0;
  }

  function fmtVND(n) {
    return (n || 0).toLocaleString('vi-VN') + ' đ';
  }

  function $id(id) {
    return document.getElementById(id);
  }

  function updateTotal() {
    const priceEl = $id('bn-price');
    const qtyEl   = $id('bn-qty');
    const totalEl = $id('bn-total');
    if (!priceEl || !qtyEl || !totalEl) return;

    const nPrice = toNumberPrice(priceEl.dataset.rawPrice ?? priceEl.textContent);
    const q = Math.max(1, parseInt(qtyEl.value || '1', 10));
    totalEl.textContent = fmtVND(nPrice * q);
  }

  function populateBuyNowModal({ id, name, price, image }) {
    const nPrice  = toNumberPrice(price);
    const nameEl  = $id('bn-name');
    const priceEl = $id('bn-price');
    const imgEl   = $id('bn-image');
    const qtyEl   = $id('bn-qty');
    const pidEl   = $id('bn-product-id');

    if (nameEl) nameEl.textContent = name ?? '';
    if (priceEl) {
      priceEl.textContent = fmtVND(nPrice);
      priceEl.dataset.rawPrice = String(nPrice);
    }
    if (imgEl) {
      const fb = imgEl.dataset.fallback || imgEl.getAttribute('src') || FALLBACK_IMG;
      imgEl.src = image || fb;
      imgEl.alt = name || 'product';
    }
    if (qtyEl) qtyEl.value = 1;
    if (pidEl) pidEl.value = id ?? '';
    updateTotal();
  }

  window.openBuyNowModal = function (id, name, price, image) {
    const modal = $id('buyNowModal');
    if (!modal) return;

    populateBuyNowModal({ id, name, price, image });

    try {
      if (window.bootstrap?.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modal).show();
      } else {
        throw new Error('no bootstrap');
      }
    } catch {
      modal.style.display = 'flex';
      modal.classList.add('show');
      modal.setAttribute('aria-modal', 'true');
      modal.removeAttribute('aria-hidden');
    }
  };

  document.addEventListener('show.bs.modal', function (ev) {
    const modal = ev.target;
    if (modal.id !== 'buyNowModal') return;

    const btn = ev.relatedTarget;
    if (!btn) return;

    populateBuyNowModal({
      id:    Number(btn.dataset.id || 0),
      name:  btn.dataset.name || '',
      price: btn.dataset.price || '0',
      image: btn.dataset.image || ''
    });
  });

  document.addEventListener('click', function (e) {
    const t = e.target.closest('.thumb');
    if (!t) return;

    const src  = t.dataset.src;
    const main = $id('mainImg');
    const btn  = $id('btnBuyNow');

    if (src && main) main.src = src;
    if (src && btn)  btn.dataset.image = src;

    const modal = $id('buyNowModal');
    if (modal && modal.classList.contains('show')) {
      const imgEl = $id('bn-image');
      if (imgEl) imgEl.src = src || (imgEl.dataset.fallback || FALLBACK_IMG);
    }
  });

  window.increaseQty = function () {
    const qty = $id('bn-qty');
    if (!qty) return;
    qty.value = Math.max(1, parseInt(qty.value || '1', 10) + 1);
    updateTotal();
  };

  window.decreaseQty = function () {
    const qty = $id('bn-qty');
    if (!qty) return;
    qty.value = Math.max(1, parseInt(qty.value || '1', 10) - 1);
    updateTotal();
  };

  document.addEventListener('input', function (e) {
    if ((e.target instanceof HTMLInputElement) && e.target.id === 'bn-qty') {
      updateTotal();
    }
  });

  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form && form.id === 'bn-form') {
      const q = $id('bn-qty')?.value || 1;
      const qHidden = $id('bn-qty-hidden');
      if (qHidden) qHidden.value = Math.max(1, parseInt(q, 10));
    }
  });

  window.closeBNModal = function () {
    const modal = $id('buyNowModal');
    if (!modal) return;

    try {
      const inst = window.bootstrap?.Modal?.getInstance?.(modal);
      if (inst) inst.hide();
      else throw new Error('no inst');
    } catch {
      modal.style.display = 'none';
      modal.classList.remove('show');
      modal.setAttribute('aria-hidden', 'true');
      modal.removeAttribute('aria-modal');
    }
  };
})();
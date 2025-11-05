(function () {
  'use strict';

  if (window.__ADD_TO_CART_BOUND__) return;
  window.__ADD_TO_CART_BOUND__ = true;

  // === UI helpers ===
  function showToast(message) {
    let el = document.getElementById('toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'toast';
      el.className = 'toast-banner';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      document.body.appendChild(el);
    }
    el.textContent = message || 'Đã thêm vào giỏ hàng';
    el.classList.add('show');
    clearTimeout(el._h);
    el._h = setTimeout(() => el.classList.remove('show'), 2200);
  }

  // === Badge helpers ===
  function setCartBadge(count) {
    const n = Math.max(0, Number(count || 0));
    const nodes = document.querySelectorAll('#cartCount, [data-cart-count], [data-cart-badge]');
    if (!nodes.length) return;

    nodes.forEach(badge => {
      if (!badge.hasAttribute('data-count-only')) {
        badge.textContent = String(n);
      }
      badge.setAttribute('data-count', String(n));
      badge.dataset.count = String(n);

      badge.classList.toggle('is-empty', n === 0);
      badge.classList.toggle('is-filled', n > 0);

      badge.classList.remove('bump');
      void badge.offsetWidth;
      badge.classList.add('bump');
    });
  }

  function getCartCount() {
    const el = document.querySelector('#cartCount, [data-cart-count], [data-cart-badge]');
    if (!el) return 0;
    const a = parseInt(el.getAttribute('data-count') || el.dataset.count || '0', 10);
    if (Number.isFinite(a)) return a;
    const t = parseInt((el.textContent || '0').replace(/[^\d]/g, ''), 10);
    return Number.isFinite(t) ? t : 0;
  }

  async function refreshCartCount() {
    try {
      const res = await fetch('/cart/count', {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'Cache-Control': 'no-store' },
        credentials: 'same-origin',
        cache: 'no-store'
      });
      if (!res.ok) return;
      const data = await res.json();
      if (typeof data.count === 'number') setCartBadge(data.count);
    } catch (e) {
      console.error('refreshCartCount error:', e);
    }
  }

  async function fetchWithTimeout(input, init = {}, ms = 12000) {
    const controller = new AbortController();
    const t = setTimeout(() => controller.abort(), ms);
    try {
      return await fetch(input, { ...init, signal: controller.signal });
    } finally {
      clearTimeout(t);
    }
  }

  function optimisticBump(qty, message) {
    const prev = getCartCount();
    const next = prev + Math.max(1, qty || 1);
    setCartBadge(next);
    showToast(message || 'Đã thêm vào giỏ hàng');

    document.dispatchEvent(new CustomEvent('cart:added', {
      detail: { quantity: qty || 1, optimisticCount: next }
    }));
  }

  async function addToCart(action, formData, opts = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const res = await fetchWithTimeout(action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: formData,
      credentials: 'same-origin',
    });

    let data = null;
    try { data = await res.json(); } catch {}

    const ok = (data && (data.success === true || data.status === 'success')) || res.ok;
    if (!ok) {
      const msg = data?.message ||
        (res.status === 419 ? 'Phiên CSRF đã hết hạn, vui lòng tải lại trang.' :
         res.status === 401 ? 'Bạn cần đăng nhập để thêm vào giỏ.' :
         `Không thể thêm vào giỏ (HTTP ${res.status}).`);
      throw new Error(msg);
    }

    if (typeof data?.count === 'number') {
      setCartBadge(data.count);
    } else if (opts.sync !== false) {
      refreshCartCount();
    }

    if (data?.message) showToast(data.message);

    document.dispatchEvent(new CustomEvent('cart:added:confirmed', {
      detail: { data }
    }));

    return data;
  }

  document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!form.classList?.contains('js-add-to-cart')) return;
    e.preventDefault();

    const isLoggedIn = document.body.classList.contains('logged-in');
    if (!isLoggedIn) { window.location.href = '/login'; return; }

    if (form.dataset.loading === '1') return;
    form.dataset.loading = '1';

    const btn = form.querySelector('button[type="submit"], [type="submit"]');
    const prevText = btn?.innerHTML;
    if (btn) { btn.disabled = true; btn.innerHTML = 'Đang thêm...'; }

    const qtyField = form.querySelector('[name="quantity"]');
    const qty = parseInt(qtyField?.value || '1', 10) || 1;
    optimisticBump(qty);

    try {
      const action = form.action || '/cart/add';
      const fd = new FormData(form);
      await addToCart(action, fd);
    } catch (err) {
      refreshCartCount();
      alert(err?.message || 'Có lỗi xảy ra, vui lòng thử lại.');
      console.error('[add-to-cart submit] error:', err);
    } finally {
      if (btn) { btn.disabled = false; btn.innerHTML = prevText; }
      form.dataset.loading = '0';
    }
  });

  document.addEventListener('click', async (e) => {
    const el = e.target.closest('.js-add-to-cart');
    if (!el) return;

    const form = el.closest('form');
    if (form && form.classList.contains('js-add-to-cart')) return;

    e.preventDefault();

    const isLoggedIn = document.body.classList.contains('logged-in');
    if (!isLoggedIn) { window.location.href = '/login'; return; }

    if (el.dataset.loading === '1') return;
    el.dataset.loading = '1';
    const prev = el.innerHTML;
    el.setAttribute('disabled', 'true');
    el.innerHTML = 'Đang thêm...';

    const qty = parseInt(el.getAttribute('data-quantity') || '1', 10) || 1;
    const optimisticToast = el.getAttribute('data-toast') || 'Đã thêm vào giỏ hàng';
    optimisticBump(qty, optimisticToast);

    try {
      const action = el.getAttribute('data-action') || el.getAttribute('href') || '/cart/add';
      const fd = new FormData();
      const productId = el.getAttribute('data-product-id');
      if (productId) fd.append('product_id', productId);
      fd.append('quantity', String(qty));
      const variantId = el.getAttribute('data-variant-id');
      if (variantId) fd.append('variant_id', variantId);
      await addToCart(action, fd);
    } catch (err) {
      refreshCartCount();
      alert(err?.message || 'Có lỗi xảy ra, vui lòng thử lại.');
      console.error('[add-to-cart click] error:', err);
    } finally {
      el.dataset.loading = '0';
      el.innerHTML = prev;
      el.removeAttribute('disabled');
    }
  });

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-remove-from-cart');
    if (!btn) return;

    e.preventDefault();

    if (btn.dataset.loading === '1') return;
    btn.dataset.loading = '1';

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const action = btn.getAttribute('data-action') || '/cart/remove';
    const productId = btn.getAttribute('data-product-id');
    const variantId = btn.getAttribute('data-variant-id');

    const prevHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Đang xoá...';

    try {
      const fd = new FormData();
      fd.append('product_id', productId);
      if (variantId) fd.append('variant_id', variantId);

      const res = await fetch(action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: fd,
        credentials: 'same-origin',
      });

      let data = null;
      try { data = await res.json(); } catch {}

      if (res.status === 401) {
        window.location.href = '/login';
        return;
      }

      if (!res.ok || data?.success === false) {
        throw new Error(data?.message || `Xoá thất bại (HTTP ${res.status})`);
      }

      const row = btn.closest('[data-cart-row]') || document.querySelector(`[data-cart-row][data-product-id="${productId}"]`);
      if (row) row.remove();

      if (typeof data?.count === 'number') setCartBadge(data.count);
      else await refreshCartCount();

      showToast(data?.message || 'Đã xóa khỏi giỏ.');
    } catch (err) {
      alert(err?.message || 'Có lỗi xảy ra, vui lòng thử lại.');
      console.error('[remove-from-cart] error:', err);
    } finally {
      btn.disabled = false;
      btn.innerHTML = prevHTML;
      btn.dataset.loading = '0';
    }
  });


  const onReady = () => refreshCartCount();
  document.addEventListener('DOMContentLoaded', onReady);
  document.addEventListener('turbo:load', onReady);
  document.addEventListener('turbolinks:load', onReady);
  document.addEventListener('livewire:load', onReady);
})();

// public/js/order.js
(function () {
  "use strict";

  // ================== COMMON ==================

  function getCsrf() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  function getRoot() {
    return document.getElementById('orders-root');
  }

  function getPaidUrl(orderId) {
    const root = getRoot();
    const tmpl = root ? root.dataset.paidUrlTemplate : '';
    return tmpl ? String(tmpl).replace('__ID__', orderId) : '';
  }

  function getRefundUrl(orderId) {
    const root = getRoot();
    const tmpl = root ? root.dataset.refundUrlTemplate : '';
    return tmpl ? String(tmpl).replace('__ID__', orderId) : '';
  }

  async function patchJSON(url, payload, fallbackMessage) {
    const res = await fetch(url, {
      method: 'PATCH',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrf(),
      },
      body: JSON.stringify(payload || {})
    });

    let data = null;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { data = await res.json(); } catch (_) {}
    }

    if (!res.ok) {
      const msg = (data && (data.message || data.error)) || fallbackMessage || 'Có lỗi xảy ra.';
      const err = new Error(msg);
      err.response = res;
      err.data = data;
      throw err;
    }

    return data;
  }

  function setButtonLoading(button, loadingText) {
    if (!button) return;
    button.disabled = true;
    button.dataset._text = button.textContent;
    button.textContent = loadingText || 'Đang xử lý...';
  }

  function restoreButton(button) {
    if (!button) return;
    button.disabled = false;
    if (button.dataset._text) {
      button.textContent = button.dataset._text;
    }
  }

  // ================== ADMIN / BACKOFFICE ==================

  window.submitBulk = function () {
    const actionEl = document.getElementById('bulk-action');
    const action = actionEl ? actionEl.value : '';

    if (!action) {
      alert('Vui lòng chọn thao tác hàng loạt.');
      return;
    }

    const checked = document.querySelectorAll('.chk-row:checked');
    if (!checked.length) {
      alert('Vui lòng chọn ít nhất 1 đơn hàng.');
      return;
    }

    const hiddenAction = document.getElementById('bulk-action-hidden');
    const idsBox = document.getElementById('bulk-ids');
    const form = document.getElementById('bulk-form');

    if (!hiddenAction || !idsBox || !form) return;

    hiddenAction.value = action;
    idsBox.innerHTML = '';

    checked.forEach((chk) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ids[]';
      input.value = chk.value;
      idsBox.appendChild(input);
    });

    form.submit();
  };

  function initCheckAll() {
    const all = document.getElementById('chk-all');
    if (!all) return;

    all.addEventListener('change', function () {
      document.querySelectorAll('.chk-row').forEach((chk) => {
        chk.checked = all.checked;
      });
    });
  }

  function initConfirmForms() {
    document.querySelectorAll('.js-confirm-form').forEach((form) => {
      form.addEventListener('submit', function (e) {
        const message = form.dataset.confirm || 'Bạn có chắc chắn?';
        if (!confirm(message)) {
          e.preventDefault();
        }
      });
    });
  }

  // dùng được cho onclick="togglePaid(id, paid)"
  window.togglePaid = async function (orderId, paid) {
    const url = getPaidUrl(orderId);
    if (!url) {
      alert('Không tìm thấy URL cập nhật.');
      return;
    }

    const active = document.activeElement;
    if (active && active.tagName === 'BUTTON') {
      setButtonLoading(active, 'Đang lưu...');
    }

    try {
      await patchJSON(url, { paid: !!paid }, 'Không thể cập nhật thanh toán.');
      location.reload();
    } catch (e) {
      console.error(e);
      alert(e.message || 'Không thể cập nhật thanh toán.');
    } finally {
      if (active && active.tagName === 'BUTTON') {
        restoreButton(active);
      }
    }
  };

  // dùng được cho onclick="toggleRefund(id, refunded)"
  window.toggleRefund = async function (orderId, refunded) {
    const url = getRefundUrl(orderId);
    if (!url) {
      alert('Không tìm thấy URL hoàn tiền.');
      return;
    }

    const active = document.activeElement;
    if (active && active.tagName === 'BUTTON') {
      setButtonLoading(active, 'Đang lưu...');
    }

    try {
      const note = refunded
        ? prompt('Nhập ghi chú hoàn tiền (có thể bỏ trống):', '')
        : '';

      await patchJSON(
        url,
        {
          refunded: !!refunded,
          note: note ?? ''
        },
        'Không thể cập nhật hoàn tiền.'
      );

      location.reload();
    } catch (e) {
      console.error(e);
      alert(e.message || 'Không thể cập nhật hoàn tiền.');
    } finally {
      if (active && active.tagName === 'BUTTON') {
        restoreButton(active);
      }
    }
  };

  // ================== CONFIRM PAGE (MUA NGAY / XÁC NHẬN ĐƠN) ==================

  function formatVND(n) {
    const num = Number(n) || 0;
    return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
  }

  function initConfirmPage() {
    const form = document.getElementById('cf-form');
    const qtyInput = document.getElementById('cf-qty');
    const hiddenQty = document.getElementById('cf-qty-hidden');
    const minusBtn = document.getElementById('cf-minus');
    const plusBtn = document.getElementById('cf-plus');
    const totalEl = document.getElementById('cf-total');
    const bankBox = document.getElementById('bank-box');
    const bankAmount = document.getElementById('bank-amount');
    const subtotalEl = document.getElementById('cf-subtotal');
    const discountEl = document.getElementById('cf-discount');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

    const promoCard = document.getElementById('promo-card');
    const promoInput = document.getElementById('cf-promo-code');
    const promoMsg = document.getElementById('cf-promo-message');
    const btnPromoApply = document.getElementById('cf-promo-apply');
    const btnPromoRemove = document.getElementById('cf-promo-remove');

    const promoApplyUrl = promoCard ? promoCard.dataset.promoApplyUrl : null;
    const promoRemoveUrl = promoCard ? promoCard.dataset.promoRemoveUrl : null;

    if (!totalEl) return;

    const hasQtyControls = !!(qtyInput || hiddenQty);

    const unitPrice = hasQtyControls
      ? (parseInt(totalEl.dataset.unit || '0', 10) || 0)
      : 0;

    function syncBankAmount() {
      if (bankAmount && totalEl) {
        bankAmount.textContent = totalEl.textContent;
      }
    }

    function syncQty(newQty) {
      if (!hasQtyControls) {
        syncBankAmount();
        return;
      }

      let q = parseInt(newQty, 10);
      if (isNaN(q) || q < 1) q = 1;

      if (qtyInput) qtyInput.value = q;
      if (hiddenQty) hiddenQty.value = q;

      const total = unitPrice * q;
      if (totalEl && !totalEl.dataset.lockedByPromo) {
        totalEl.textContent = formatVND(total);
      }

      syncBankAmount();
    }

    if (minusBtn) {
      minusBtn.addEventListener('click', function () {
        const cur = parseInt(
          (qtyInput && qtyInput.value) ||
          (hiddenQty && hiddenQty.value) ||
          '1',
          10
        );
        syncQty(cur - 1);
      });
    }

    if (plusBtn) {
      plusBtn.addEventListener('click', function () {
        const cur = parseInt(
          (qtyInput && qtyInput.value) ||
          (hiddenQty && hiddenQty.value) ||
          '1',
          10
        );
        syncQty(cur + 1);
      });
    }

    if (qtyInput) {
      qtyInput.addEventListener('input', function () {
        syncQty(qtyInput.value);
      });
    }

    if (hasQtyControls) {
      syncQty(
        (qtyInput && qtyInput.value) ||
        (hiddenQty && hiddenQty.value) ||
        '1'
      );
    } else {
      syncBankAmount();
    }

    if (bankBox && paymentRadios.length) {
      function toggleBankBox() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        const show = checked && checked.value === 'bank';
        bankBox.classList.toggle('d-none', !show);
        if (show) syncBankAmount();
      }

      paymentRadios.forEach(r => r.addEventListener('change', toggleBankBox));
      toggleBankBox();
    }

    function setPromoMsg(text, type) {
      if (!promoMsg) return;
      promoMsg.textContent = text || '';
      promoMsg.classList.remove('text-muted', 'text-success', 'text-danger');
      promoMsg.classList.add(type || 'text-muted');
    }

    async function callPromo(url, payload) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrf(),
        },
        body: JSON.stringify(payload || {})
      });

      let data = null;
      try { data = await res.json(); } catch (_) {}

      if (!res.ok) {
        const msg = data && (data.message || data.error) || 'Không áp dụng được mã.';
        throw new Error(msg);
      }

      return data;
    }

    function applyTotalsFromResponse(data) {
      const t = data && (data.totals || data);
      if (!t || !totalEl) return;

      if (subtotalEl && t.subtotal) {
        subtotalEl.textContent = t.subtotal;
      }

      if (discountEl && typeof t.discount !== 'undefined') {
        discountEl.textContent = '- ' + t.discount;
      }

      if (t.total) {
        totalEl.textContent = t.total;
        totalEl.dataset.lockedByPromo = '1';
      }

      syncBankAmount();
    }

    if (btnPromoApply && promoInput && promoApplyUrl) {
      btnPromoApply.addEventListener('click', async function () {
        const code = (promoInput.value || '').trim();
        if (!code) {
          setPromoMsg('Vui lòng nhập mã khuyến mãi.', 'text-danger');
          return;
        }

        setPromoMsg('Đang áp dụng mã...', 'text-muted');

        try {
          const data = await callPromo(promoApplyUrl, {
            code,
            quantity: qtyInput ? Number(qtyInput.value || 1) : 1
          });

          applyTotalsFromResponse(data);
          setPromoMsg(data.message || 'Áp dụng mã khuyến mãi thành công.', 'text-success');
          if (btnPromoRemove) btnPromoRemove.classList.remove('d-none');
        } catch (e) {
          console.error(e);
          setPromoMsg(e.message || 'Không áp dụng được mã.', 'text-danger');
          if (totalEl) delete totalEl.dataset.lockedByPromo;
        }
      });
    }

    if (btnPromoRemove && promoRemoveUrl) {
      btnPromoRemove.addEventListener('click', async function () {
        setPromoMsg('Đang hủy mã...', 'text-muted');

        try {
          const data = await callPromo(promoRemoveUrl, {});
          applyTotalsFromResponse(data);
          if (promoInput) promoInput.value = '';
          setPromoMsg(data.message || 'Đã hủy mã khuyến mãi.', 'text-success');
          if (totalEl) delete totalEl.dataset.lockedByPromo;
          if (btnPromoRemove) btnPromoRemove.classList.add('d-none');
        } catch (e) {
          console.error(e);
          setPromoMsg(e.message || 'Không thể hủy mã.', 'text-danger');
        }
      });
    }
  }

  function copyText(text) {
    navigator.clipboard?.writeText(String(text)).then(() => {
      alert('Đã copy: ' + text);
    }).catch(() => {
      const t = document.createElement('textarea');
      t.value = text;
      document.body.appendChild(t);
      t.select();
      document.execCommand('copy');
      document.body.removeChild(t);
      alert('Đã copy: ' + text);
    });
  }

  window.copyText = copyText;

  // ================== INIT ==================

  document.addEventListener('DOMContentLoaded', function () {
    initCheckAll();
    initConfirmForms();
    initConfirmPage();
  });

})();
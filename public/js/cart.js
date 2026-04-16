(function () {
  "use strict";

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || "";

  async function requestJson(method, url, payload) {
    const opts = {
      method,
      headers: {
        Accept: "application/json",
        "X-CSRF-TOKEN": CSRF,
      },
    };

    if (payload) {
      opts.headers["Content-Type"] = "application/json";
      opts.body = JSON.stringify(payload);
    }

    const res = await fetch(url, opts);

    let data = null;
    const ct = res.headers.get("content-type") || "";
    if (ct.includes("application/json")) {
      try { data = await res.json(); } catch (_) { data = null; }
    }

    if (!res.ok) {
      const msg = (data && (data.message || data.error)) || "Có lỗi khi xử lý yêu cầu.";
      const err = new Error(msg);
      err.response = res;
      err.data = data;
      throw err;
    }

    return data;
  }

  function updateSummary(totals) {
    if (!totals) return;

    const subtotalEl = document.getElementById("cart-subtotal");
    const discountEl = document.getElementById("cart-discount");
    const totalEl = document.getElementById("cart-total");

    if (subtotalEl && typeof totals.subtotal !== "undefined") {
      subtotalEl.textContent = totals.subtotal;
    }

    if (discountEl && typeof totals.discount !== "undefined") {
      discountEl.textContent = totals.discount;
    }

    if (totalEl && typeof totals.total !== "undefined") {
      totalEl.textContent = totals.total;
    }
  }

  function updateCartCount(count) {
    const badge = document.getElementById("cartCount");
    if (!badge) return;

    const num = Number(count) || 0;
    badge.dataset.count = num;
    badge.textContent = num;

    if (num > 0) badge.classList.remove("d-none");
    else badge.classList.add("d-none");
  }

  async function updateRowQuantity(row, qty) {
    const url = row.dataset.updateUrl;
    if (!url) return;

    const input = row.querySelector(".js-qty-input");
    const qtyNum = Math.max(1, parseInt(qty || input?.value || "1", 10));
    const productId = row.dataset.productId;

    try {
      const data = await requestJson("POST", url, {
        product_id: productId,
        qty: qtyNum,
        quantity: qtyNum,
      });

      if (input) {
        input.value = (typeof data.qty !== "undefined") ? data.qty : qtyNum;
      }

      const lineEl = row.querySelector(".js-item-total");
      if (lineEl) {
        if (data.item_total) lineEl.textContent = data.item_total;
        else if (data.line_subtotal) lineEl.textContent = data.line_subtotal;
      }

      if (data.totals) {
        updateSummary(data.totals);
        updateCartCount(data.totals.count);
      }
    } catch (err) {
      console.error(err);
      alert(err.message || "Không thể cập nhật số lượng.");
    }
  }

  async function removeRow(row) {
    const url = row.dataset.removeUrl;
    if (!url) return;

    if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) return;

    try {
      const data = await requestJson("POST", url);
      if (data.ok === false || data.success === false) {
        alert(data.message || "Không thể xóa sản phẩm.");
        return;
      }

      row.remove();

      if (data.totals) {
        updateSummary(data.totals);
        updateCartCount(data.totals.count);
      }

      const rows = document.querySelectorAll("[data-cart-row]");
      const title = document.querySelector("h4 .text-muted");
      if (title) {
        title.textContent = `(${rows.length} sản phẩm)`;
      }
    } catch (err) {
      console.error(err);
      alert(err.message || "Không thể xóa sản phẩm.");
    }
  }

  document.addEventListener("click", function (e) {
    const minusBtn = e.target.closest(".js-qty-minus");
    if (minusBtn) {
      const row = minusBtn.closest("[data-cart-row]");
      const input = row?.querySelector(".js-qty-input");
      if (!input) return;

      const cur = Math.max(2, parseInt(input.value || "1", 10));
      input.value = cur - 1;
      return;
    }

    const plusBtn = e.target.closest(".js-qty-plus");
    if (plusBtn) {
      const row = plusBtn.closest("[data-cart-row]");
      const input = row?.querySelector(".js-qty-input");
      if (!input) return;

      const cur = Math.max(1, parseInt(input.value || "1", 10));
      input.value = cur + 1;
      return;
    }

    const updateBtn = e.target.closest(".js-qty-update");
    if (updateBtn) {
      const row = updateBtn.closest("[data-cart-row]");
      const input = row?.querySelector(".js-qty-input");
      if (!row || !input) return;

      updateRowQuantity(row, input.value);
      return;
    }

    const removeBtn = e.target.closest(".js-remove-from-cart");
    if (removeBtn) {
      const row = removeBtn.closest("[data-cart-row]");
      if (!row) return;

      removeRow(row);
      return;
    }
  });

  document.addEventListener("change", function (e) {
    const input = e.target;
    if (!input.classList.contains("js-qty-input")) return;

    input.value = Math.max(1, parseInt(input.value || "1", 10));
  });

  function updateSelectedProducts() {
    const hidden = document.getElementById("selectedProducts");
    if (!hidden) return;

    const ids = [...document.querySelectorAll(".js-item-check:checked")]
      .map(ch => ch.dataset.productId);

    hidden.value = ids.join(",");
  }

  document.querySelectorAll(".js-item-check")
    .forEach(ch => ch.addEventListener("change", updateSelectedProducts));

  const selectAll = document.getElementById("selectAll");
  if (selectAll) {
    selectAll.addEventListener("change", function () {
      const checked = this.checked;
      document.querySelectorAll(".js-item-check").forEach(ch => ch.checked = checked);
      updateSelectedProducts();
    });
  }

  const checkoutForm = document.getElementById("checkoutForm");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", function () {
      const checked = [...document.querySelectorAll(".js-item-check:checked")]
        .map(ch => ch.dataset.productId);

      const ids = checked.length
        ? checked
        : [...document.querySelectorAll(".js-item-check")].map(ch => ch.dataset.productId);

      const hidden = document.getElementById("selectedProducts");
      if (hidden) hidden.value = ids.join(",");
    });
  }

  updateSelectedProducts();
})();
// public/js/detail.js

(function () {
  /* ================= Helper ================= */
  function csrfToken() {
    var el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  /* ========== Trang CHI TIẾT: đổi ảnh chính ========== */
  // Event delegation để luôn hoạt động, kể cả khi DOM thay đổi
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.thumb');
    if (!btn) return;

    var src = btn.getAttribute('data-src');
    var main = document.getElementById('mainImg');
    if (src && main) {
      main.src = src;
      // trạng thái active
      document.querySelectorAll('.thumb').forEach(function (b) {
        b.classList.remove('thumb--active', 'border-pink');
      });
      btn.classList.add('thumb--active', 'border-pink');
    }
  });

  // Optional: cho phép dùng fallback inline nếu muốn
  window.changeMainImage = function (src, btn) {
    var main = document.getElementById('mainImg');
    if (!main || !src) return;
    main.src = src;
    try {
      document.querySelectorAll('.thumb').forEach(function (b) {
        b.classList.remove('thumb--active', 'border-pink');
      });
      if (btn) btn.classList.add('thumb--active', 'border-pink');
    } catch (e) {}
  };

  /* ========== Trang CHI TIẾT: AJAX add to cart ========== */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.classList.contains('js-add-to-cart')) return;

    e.preventDefault();
    var fd = new FormData(form);
    var action = form.getAttribute('action') || window.location.href;

    fetch(action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        // Lấy từ <meta>, nếu không có sẽ dùng _token từ form
        'X-CSRF-TOKEN': csrfToken() || fd.get('_token') || ''
      },
      body: fd
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        alert('Đã thêm vào giỏ hàng!');
      })
      .catch(function (err) {
        console.error(err);
        alert('Thêm vào giỏ thất bại, vui lòng thử lại.');
      });
  });

  function initNewImagesPreview() {
    var input = document.getElementById('imagesInput');
    var preview = document.getElementById('previewNew');
    if (!input || !preview) return; // không phải trang create/edit

    input.addEventListener('change', function (e) {
      // reset
      preview.innerHTML = '';
      var files = Array.from(e.target.files || []);

      files.forEach(function (file, idx) {
        var url = URL.createObjectURL(file);

        var card = document.createElement('div');
        card.className = 'border rounded p-2 bg-white';
        card.innerHTML = `
          <img src="${url}" class="w-full aspect-square object-cover rounded mb-2" alt="new">
          <label class="block text-sm mb-2">
            ALT:
            <input type="text" name="alt_new[${idx}]" class="w-full border rounded px-2 py-1" placeholder="Mô tả ảnh #${idx + 1}">
          </label>
          <label class="block text-sm">
            Thứ tự:
            <input type="number" name="sort_new[${idx}]" class="w-full border rounded px-2 py-1" value="${idx}">
          </label>
        `;
        preview.appendChild(card);
      });
    });
  }
  // Gọi ngay (nếu có input)
  initNewImagesPreview();


document.getElementById('imagesInput')?.addEventListener('change', function (e) {
    const preview = document.getElementById('previewNew');
    if (!preview) return;
    preview.innerHTML = '';
    Array.from(e.target.files || []).forEach(file => {
        const url = URL.createObjectURL(file);
        const wrap = document.createElement('div');
        wrap.innerHTML = `<img src="${url}" class="w-full aspect-square object-cover rounded border" alt="new">`;
        preview.appendChild(wrap);
    });
});

})();

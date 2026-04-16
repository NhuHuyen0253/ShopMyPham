(function () {
  function csrfToken() {
    var el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.thumb');
    if (!btn) return;

    var src = btn.getAttribute('data-src');
    var main = document.getElementById('mainImg');
    if (src && main) {
      main.src = src;
      document.querySelectorAll('.thumb').forEach(function (b) {
        b.classList.remove('thumb--active', 'border-pink');
      });
      btn.classList.add('thumb--active', 'border-pink');
    }
  });

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
    if (!input || !preview) return;

    input.addEventListener('change', function (e) {
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

  document.addEventListener('DOMContentLoaded', function () {
    const box = document.getElementById('descMd');
    if (box) {
      const headings = Array.from(box.querySelectorAll('h1,h2,h3'));
      let ingTitle = headings.find(h =>
        h.textContent.trim().toLowerCase().includes('thành phần sản phẩm')
      );

      if (!ingTitle) {
        const strongP = Array.from(box.querySelectorAll('p > strong')).find(el =>
          el.textContent.trim().toLowerCase().includes('thành phần sản phẩm')
        );
        if (strongP) ingTitle = strongP.closest('p');
      }

      if (ingTitle) {
        ingTitle.classList.add('md-ing-title');
        let el = ingTitle.nextElementSibling;
        while (el && !/^H[1-6]$/.test(el.tagName)) {
          el.classList.add('md-ing');
          el = el.nextElementSibling;
        }
      }
    }

    if (typeof Swiper !== 'undefined' && document.querySelector('.relatedProductsSwiper')) {
      new Swiper('.relatedProductsSwiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        grabCursor: true,
        navigation: {
          nextEl: '.related-swiper-next',
          prevEl: '.related-swiper-prev',
        },
        pagination: {
          el: '.related-swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          576: {
            slidesPerView: 2,
            spaceBetween: 16,
          },
          768: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
          992: {
            slidesPerView: 4,
            spaceBetween: 24,
          }
        }
      });
    }
  });
  
document.addEventListener('DOMContentLoaded', function () {
    const labels = document.querySelectorAll('.star-rating label');
    const inputs = document.querySelectorAll('.star-rating input');
    const note = document.getElementById('ratingNote');

    function getText(value) {
        value = Number(value);
        if (value === 1) return '1 sao - Rất không hài lòng';
        if (value === 2) return '2 sao - Chưa hài lòng';
        if (value === 3) return '3 sao - Bình thường';
        if (value === 4) return '4 sao - Hài lòng';
        return '5 sao - Rất hài lòng';
    }

    labels.forEach(label => {
        label.addEventListener('mouseenter', function () {
            const value = this.getAttribute('for').replace('star', '');
            note.textContent = getText(value);
        });
    });

    document.querySelector('.star-rating').addEventListener('mouseleave', function () {
        const checked = document.querySelector('.star-rating input:checked');
        note.textContent = checked ? getText(checked.value) : 'Chọn số sao đánh giá';
    });

    inputs.forEach(input => {
        input.addEventListener('change', function () {
            note.textContent = getText(this.value);
        });
    });

    const checked = document.querySelector('.star-rating input:checked');
    if (checked) {
        note.textContent = getText(checked.value);
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.capacity-input');
    const labelText = document.querySelector('.fw-semibold.text-dark.mb-2 span');

    inputs.forEach(input => {
        input.addEventListener('change', function () {
            if (labelText) {
                labelText.textContent = this.value;
            }
        });
    });
});
})();
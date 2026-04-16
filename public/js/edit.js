document.addEventListener('DOMContentLoaded', () => {
  initEditors();
  initAvatarPreview();
  initImagePreview();
  initDeleteStoredImages();
  initHotDealToggle();
});

function initEditors() {
  if (typeof ClassicEditor === 'undefined') return;

  ['#description', '#usage_instructions'].forEach((selector) => {
    const el = document.querySelector(selector);
    if (!el) return;

    ClassicEditor.create(el, {
      toolbar: [
        'heading', '|',
        'bold', 'italic', '|',
        'bulletedList', 'numberedList', '|',
        'blockQuote', '|',
        'undo', 'redo'
      ]
    }).catch((error) => {
      console.error('CKEditor error:', error);
    });
  });
}

function initAvatarPreview() {
  const imageInput = document.getElementById('image');
  if (!imageInput) return;

  let previewBox = document.getElementById('avatarPreview');

  if (!previewBox) {
    previewBox = document.createElement('div');
    previewBox.id = 'avatarPreview';
    imageInput.insertAdjacentElement('afterend', previewBox);
  }

  imageInput.addEventListener('change', () => {
    previewBox.innerHTML = '';

    const file = imageInput.files?.[0];
    if (!file || !file.type.startsWith('image/')) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'mt-3 inline-block border rounded-xl p-2 bg-gray-50';

    const img = document.createElement('img');
    img.className = 'h-28 w-28 object-cover rounded-lg border';

    const info = document.createElement('div');
    info.className = 'mt-2 text-xs text-gray-600';
    info.textContent = file.name;

    const reader = new FileReader();
    reader.onload = (e) => {
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    wrapper.appendChild(img);
    wrapper.appendChild(info);
    previewBox.appendChild(wrapper);
  });
}

function initImagePreview() {
  const imagesInput = document.getElementById('imagesInput');
  const previewNew = document.getElementById('previewNew');

  if (!imagesInput || !previewNew) return;

  let dt = new DataTransfer();

  function renderPreviewNew() {
    previewNew.innerHTML = '';

    if (!dt.files.length) {
      previewNew.innerHTML = `
        <div class="col-span-full text-xs text-gray-500">
          Chưa có ảnh minh hoạ mới được chọn.
        </div>
      `;
      return;
    }

    Array.from(dt.files).forEach((file, idx) => {
      if (!file.type.startsWith('image/')) return;

      const card = document.createElement('div');
      card.className = 'border rounded-2xl p-3 bg-gray-50 shadow-sm';

      const img = document.createElement('img');
      img.className = 'w-full aspect-square object-cover rounded-xl border';

      const name = document.createElement('div');
      name.className = 'mt-2 text-xs text-gray-600 break-all';
      name.textContent = file.name;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mt-2 text-sm text-red-600 hover:text-red-700 hover:underline';
      btn.textContent = 'Xóa ảnh này';

      const fr = new FileReader();
      fr.onload = (e) => {
        img.src = e.target.result;
      };
      fr.readAsDataURL(file);

      btn.addEventListener('click', () => {
        const newDT = new DataTransfer();

        Array.from(dt.files).forEach((f, i) => {
          if (i !== idx) newDT.items.add(f);
        });

        dt = newDT;
        imagesInput.files = dt.files;
        renderPreviewNew();
      });

      card.appendChild(img);
      card.appendChild(name);
      card.appendChild(btn);
      previewNew.appendChild(card);
    });
  }

  imagesInput.addEventListener('change', () => {
    dt = new DataTransfer();

    Array.from(imagesInput.files).forEach((file) => {
      if (file.type.startsWith('image/')) {
        dt.items.add(file);
      }
    });

    imagesInput.files = dt.files;
    renderPreviewNew();
  });
}

function initDeleteStoredImages() {
  const csrf =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const buttons = document.querySelectorAll('.btn-delete-image');
  if (!buttons.length) return;

  buttons.forEach((btn) => {
    btn.addEventListener('click', async () => {
      const action = btn.getAttribute('data-action');
      const imageId = btn.getAttribute('data-image-id');
      const productId = btn.getAttribute('data-product-id');
      const card = document.getElementById(`img-card-${imageId}`);

      if (!action) return;
      if (!confirm('Xóa ảnh này?')) return;

      btn.disabled = true;
      const oldText = btn.textContent;
      btn.textContent = 'Đang xóa...';

      try {
        const res = await fetch(action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            _method: 'DELETE',
            product_id: productId,
            image_id: imageId
          })
        });

        if (!res.ok) {
          let errText = 'Xóa ảnh thất bại';

          try {
            const j = await res.json();
            if (j?.message) errText = j.message;
          } catch (_) {}

          alert(errText);
          btn.disabled = false;
          btn.textContent = oldText;
          return;
        }

        if (card) card.remove();
      } catch (error) {
        console.error(error);
        alert('Có lỗi mạng khi xóa ảnh.');
        btn.disabled = false;
        btn.textContent = oldText;
      }
    });
  });
}

function initHotDealToggle() {
  const hotdealCheckbox = document.getElementById('is_hotdeal');
  const discountWrap = document.getElementById('discountWrap');
  const discountInput = document.getElementById('discount_percent');

  if (!hotdealCheckbox || !discountWrap) return;

  function toggleDiscountField() {
    if (hotdealCheckbox.checked) {
      discountWrap.style.display = 'block';
    } else {
      discountWrap.style.display = 'none';
      if (discountInput) discountInput.value = '';
    }
  }

  toggleDiscountField();
  hotdealCheckbox.addEventListener('change', toggleDiscountField);
}
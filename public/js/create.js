document.addEventListener('DOMContentLoaded', function () {
  initEditors();
  initAvatarPreview();
  initHotDealToggle();
  initGalleryPreview();
});

function initEditors() {
  if (typeof ClassicEditor === 'undefined') return;

  ['#description', '#usage_instructions'].forEach(function (selector) {
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
    }).catch(function (error) {
      console.error('CKEditor error:', error);
    });
  });
}

function initAvatarPreview() {
  const avatarInput = document.getElementById('image');
  const avatarPrev = document.getElementById('avatarPreview');

  if (!avatarInput || !avatarPrev) return;

  avatarInput.addEventListener('change', function () {
    avatarPrev.innerHTML = '';

    const file = this.files && this.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'mt-2 inline-block border rounded-xl p-2 bg-gray-50';

    const img = document.createElement('img');
    img.className = 'h-24 w-24 object-cover border rounded-lg';
    img.src = URL.createObjectURL(file);
    img.onload = () => URL.revokeObjectURL(img.src);

    const name = document.createElement('div');
    name.className = 'mt-2 text-xs text-gray-600 break-all';
    name.textContent = file.name;

    wrapper.appendChild(img);
    wrapper.appendChild(name);
    avatarPrev.appendChild(wrapper);
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

function initGalleryPreview() {
  const input = document.getElementById('images');
  const preview = document.getElementById('previewNew');
  const hint = document.getElementById('dropHint');
  const clearBtn = document.getElementById('clearAllPreviews');

  if (!input || !preview) return;

  function getImageFiles(files) {
    return Array.from(files || []).filter(file => file.type.startsWith('image/'));
  }

  function setFiles(files) {
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
  }

  function rebuildPreview(files) {
    preview.querySelectorAll('.img-card').forEach(el => el.remove());

    if (hint) {
      hint.style.display = files.length ? 'none' : '';
    }

    files.forEach((file, idx) => {
      const card = document.createElement('div');
      card.className = 'img-card relative border rounded-xl p-2 bg-gray-50 shadow-sm';

      const img = document.createElement('img');
      img.className = 'w-full h-40 object-cover rounded-lg border';
      img.src = URL.createObjectURL(file);
      img.onload = () => URL.revokeObjectURL(img.src);

      const info = document.createElement('div');
      info.className = 'mt-2 text-xs text-gray-600 break-all';
      info.textContent = file.name;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'absolute top-2 right-2 bg-white border text-red-600 text-xs px-2 py-1 rounded shadow-sm hover:bg-red-50';
      btn.textContent = 'Xoá';

      btn.addEventListener('click', function () {
        const currentFiles = getImageFiles(input.files);
        const newFiles = currentFiles.filter((_, i) => i !== idx);
        setFiles(newFiles);
        rebuildPreview(newFiles);
      });

      card.appendChild(img);
      card.appendChild(info);
      card.appendChild(btn);
      preview.appendChild(card);
    });
  }

  input.addEventListener('change', function () {
    const files = getImageFiles(input.files);
    setFiles(files);
    rebuildPreview(files);
  });

  ['dragenter', 'dragover'].forEach(eventName => {
    preview.addEventListener(eventName, function (e) {
      e.preventDefault();
      preview.classList.add('ring', 'ring-pink-300');
    });
  });

  ['dragleave', 'drop'].forEach(eventName => {
    preview.addEventListener(eventName, function (e) {
      e.preventDefault();
      preview.classList.remove('ring', 'ring-pink-300');
    });
  });

  preview.addEventListener('drop', function (e) {
    const droppedFiles = getImageFiles(e.dataTransfer.files);
    if (!droppedFiles.length) return;

    const currentFiles = getImageFiles(input.files);
    const mergedFiles = [...currentFiles, ...droppedFiles];

    setFiles(mergedFiles);
    rebuildPreview(mergedFiles);
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      setFiles([]);
      rebuildPreview([]);
    });
  }
}
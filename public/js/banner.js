console.log('banner.js loaded OK');

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded OK');

    const searchInput = document.getElementById('productSearchInput');
    const items = Array.from(document.querySelectorAll('.promo-product-item'));
    const emptyState = document.getElementById('productSearchEmpty');

    console.log('searchInput = ', searchInput);
    console.log('items count = ', items.length);

    function normalizeText(str) {
        return (str || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'd')
            .trim();
    }

    function filterProducts() {
        if (!searchInput || !items.length) return;

        const keyword = normalizeText(searchInput.value);
        let visibleCount = 0;

        items.forEach(function (item) {
            const searchText = normalizeText(item.getAttribute('data-search') || '');
            const matched = keyword === '' || searchText.includes(keyword);

            item.style.display = matched ? 'flex' : 'none';

            if (matched) visibleCount++;
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
        filterProducts();
    }
});

function selectAllVisibleProducts() {
    document.querySelectorAll('.promo-product-item').forEach(function (item) {
        if (item.style.display !== 'none') {
            const checkbox = item.querySelector('.promo-product-checkbox');
            if (checkbox) checkbox.checked = true;
        }
    });
}

function unselectAllVisibleProducts() {
    document.querySelectorAll('.promo-product-item').forEach(function (item) {
        if (item.style.display !== 'none') {
            const checkbox = item.querySelector('.promo-product-checkbox');
            if (checkbox) checkbox.checked = false;
        }
    });
}
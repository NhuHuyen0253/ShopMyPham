function initHotdealToggle() {
    const hotdealCheckbox = document.getElementById('is_hotdeal');
    const discountWrap = document.getElementById('discountWrap');

    if (!hotdealCheckbox || !discountWrap) return;

    function toggleDiscount() {
        discountWrap.style.display = hotdealCheckbox.checked ? 'block' : 'none';
    }

    hotdealCheckbox.addEventListener('change', toggleDiscount);
    toggleDiscount();
}

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
            console.error(error);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initHotdealToggle();
    initEditors();
});
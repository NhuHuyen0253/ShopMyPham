document.addEventListener('DOMContentLoaded', function () {
    // ===== Nhập kho =====
    const stockInCard = document.getElementById('stockInCard');
    const stockInProductId = document.getElementById('stockInProductId');
    const stockInProductName = document.getElementById('stockInProductName');
    const selectedProductText = document.getElementById('selectedProductText');
    const btnCloseStockIn = document.getElementById('btnCloseStockIn');
    const btnCancelStockIn = document.getElementById('btnCancelStockIn');
    const openStockInButtons = document.querySelectorAll('.btn-open-stockin');

    // ===== Xuất kho =====
    const stockOutCard = document.getElementById('stockOutCard');
    const stockOutProductId = document.getElementById('stockOutProductId');
    const stockOutProductName = document.getElementById('stockOutProductName');
    const stockOutCurrentQty = document.getElementById('stockOutCurrentQty');
    const selectedStockOutProductText = document.getElementById('selectedStockOutProductText');
    const openStockOutButtons = document.querySelectorAll('.btn-open-stockout');
    const btnCloseStockOut = document.getElementById('btnCloseStockOut');
    const btnCancelStockOut = document.getElementById('btnCancelStockOut');

    function closeStockInForm() {
        if (!stockInCard) return;

        stockInCard.classList.add('stockin-hidden');

        if (stockInProductId) stockInProductId.value = '';
        if (stockInProductName) stockInProductName.value = '';
        if (selectedProductText) selectedProductText.textContent = 'Chưa chọn sản phẩm';
    }

    function closeStockOutForm() {
        if (!stockOutCard) return;

        stockOutCard.classList.add('stockin-hidden');

        if (stockOutProductId) stockOutProductId.value = '';
        if (stockOutProductName) stockOutProductName.value = '';
        if (stockOutCurrentQty) stockOutCurrentQty.value = '';
        if (selectedStockOutProductText) selectedStockOutProductText.textContent = 'Chưa chọn sản phẩm';
    }

    // Mở form nhập kho
    openStockInButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId || '';
            const productName = this.dataset.productName || '';

            closeStockOutForm(); // ẩn form xuất kho trước

            if (stockInProductId) stockInProductId.value = productId;
            if (stockInProductName) stockInProductName.value = productName;
            if (selectedProductText) {
                selectedProductText.textContent = 'Đang nhập kho cho: ' + productName;
            }

            if (stockInCard) {
                stockInCard.classList.remove('stockin-hidden');

                setTimeout(() => {
                    stockInCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        });
    });

    // Mở form xuất kho
    openStockOutButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId || '';
            const productName = this.dataset.productName || '';
            const productQty = this.dataset.productQty || 0;

            closeStockInForm(); // ẩn form nhập kho trước

            if (stockOutProductId) stockOutProductId.value = productId;
            if (stockOutProductName) stockOutProductName.value = productName;
            if (stockOutCurrentQty) stockOutCurrentQty.value = productQty;
            if (selectedStockOutProductText) {
                selectedStockOutProductText.textContent = productName + ' (Tồn hiện tại: ' + productQty + ')';
            }

            if (stockOutCard) {
                stockOutCard.classList.remove('stockin-hidden');

                setTimeout(() => {
                    stockOutCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        });
    });

    btnCloseStockIn?.addEventListener('click', closeStockInForm);
    btnCancelStockIn?.addEventListener('click', closeStockInForm);

    btnCloseStockOut?.addEventListener('click', closeStockOutForm);
    btnCancelStockOut?.addEventListener('click', closeStockOutForm);
});
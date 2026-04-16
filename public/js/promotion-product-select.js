document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('product_ids');
    if (!select || typeof TomSelect === 'undefined') return;

    new TomSelect('#product_ids', {
        plugins: {
            remove_button: {
                title: 'Xóa'
            }
        },
        maxOptions: 500,
        hideSelected: true,
        closeAfterSelect: true,
        searchField: ['text', 'value'],
        dropdownParent: 'body',
        placeholder: 'Gõ tên sản phẩm, SKU hoặc ID...',
        render: {
            option: function (data, escape) {
                const sku = data.$option?.dataset?.sku || '';
                const price = data.$option?.dataset?.price || '';

                return `
                    <div style="padding:10px 12px;">
                        <div style="font-weight:600;">
                            ${escape(data.text)}
                        </div>
                        <div style="font-size:12px;color:#6b7280;">
                            ${sku ? 'SKU: ' + escape(sku) + ' • ' : ''}${price ? 'Giá: ' + escape(price) : ''}
                        </div>
                    </div>
                `;
            },
            item: function (data, escape) {
                return `<div>${escape(data.text)}</div>`;
            }
        }
    });
});
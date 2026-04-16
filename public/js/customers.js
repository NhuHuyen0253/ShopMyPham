document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  function updateBadge(cell, status) {
    cell.innerHTML = '';
    const badge = document.createElement('span');
    badge.className =
      'inline-flex items-center px-2 py-1 rounded text-xs ' +
      (status === 'success' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700');
    badge.textContent = status;
    cell.appendChild(badge);
  }

  document.querySelectorAll('input.paid-toggle').forEach((cb) => {
    cb.addEventListener('change', async () => {
      const url = cb.dataset.url;
      const paid = cb.checked;
      try {
        const res = await fetch(url, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: JSON.stringify({ paid }),
        });
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();

        const row = cb.closest('tr');
        const statusCell = row.querySelector('.order-status');
        if (statusCell) updateBadge(statusCell, data.status);
      } catch (e) {
        cb.checked = !paid; // revert nếu lỗi
        alert('Cập nhật thất bại. Vui lòng thử lại!');
      }
    });
  });
});

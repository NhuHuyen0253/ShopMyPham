document.addEventListener('DOMContentLoaded', function () {
      const backToTopBtn = document.getElementById('backToTopBtn');

      if (!backToTopBtn) return;

      // Hiện nút khi cuộn xuống
      window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
          backToTopBtn.classList.add('show');
        } else {
          backToTopBtn.classList.remove('show');
        }
      });

      // Cuộn mượt lên đầu trang khi bấm nút
      backToTopBtn.addEventListener('click', function () {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    });
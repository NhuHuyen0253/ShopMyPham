<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'XinhXinhShop')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('images/home/xinhxinhshop.png') }}" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  
  {{-- Vite (nếu bạn dùng) --}}
  @vite(['resources/css/app.css'])

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Thêm CSS của Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  
  <!-- CSS của bạn -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>


<body class="{{ auth()->check() ? 'logged-in' : '' }}">
 

  <!-- Top bar: khuyến mãi chạy ngang -->
  <div class="topbar">
    <div class="container promo-ticker">
      <div class="promo-track">
        <span>🌸 Freeship đơn từ 299k</span>
        <span>✨ Mua 2 tặng 1 - Trang điểm</span>
        <span>🛡️ Giảm 20% Kem chống nắng</span>
        <span>💧 Combo skincare chỉ từ 199k</span>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="py-3 bg-white">
    <div class="container">
      <div class="row g-3 align-items-center">

          <!-- Logo -->
          <div class="col-12 col-md-3">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center text-decoration-none">
              <img src="{{ asset('images/home/xinhxinhshop.png') }}" class="me-2 logo-img" alt="Logo">
            </a>
          </div>

          <!-- Search -->
          <div class="col-12 col-md">
            <form action="{{ url('/products') }}" method="get" class="d-flex gap-2 search">
              <input type="text" name="q" class="form-control form-control-lg rounded-pill"
                     placeholder="Tìm son, sữa rửa mặt, kem chống nắng...">
              <button type="submit" class="btn btn-lg btn-pink rounded-pill fw-bold">Tìm kiếm</button>
            </form>
          </div>

          <!-- Actions: Liên hệ – Tài khoản – Giỏ hàng -->
          <div class="col-12 col-md-auto">
            <div class="d-flex align-items-center gap-2 flex-nowrap">

              <a href="{{ url('/contact') }}" class="btn btn-outline-pink btn-lg rounded-pill fw-bold">Liên hệ</a>

              @auth
                <div class="dropdown">
                  <button
                    class="btn btn-pink btn-lg rounded-pill dropdown-toggle fw-bold"
                    type="button"
                    id="profileDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ \Illuminate\Support\Str::limit(Auth::user()->name, 14) }}
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown" style="min-width:220px">
                    <li><a class="dropdown-item" href="{{ route('account.info') }}">Tài khoản của tôi</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Đăng xuất</button>
                      </form>
                    </li>
                  </ul>
                </div>
              @else
                <div class="dropdown">
                  <button
                    class="btn btn-pink btn-lg rounded-pill dropdown-toggle fw-bold"
                    type="button"
                    id="accountDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Tài khoản
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="accountDropdown" style="min-width:200px">
                    <li><a class="dropdown-item fw-semibold" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li><a class="dropdown-item fw-semibold" href="{{ route('register') }}">Đăng ký</a></li>
                  </ul>
                </div>
              @endauth

              <!-- Cart Icon -->
              <div class="cart-icon">
                <a href="{{ route('cart.view') }}">
                  <i class="fa fa-shopping-cart"></i>
                </a>

                <span
                  id="cartCount"
                  class="cart-count"
                  data-count="{{ count(session()->get('cart', [])) }}"
                  aria-label="Số sản phẩm trong giỏ"
                >
                  {{ count(session()->get('cart', [])) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Navbar categories -->
  <nav class="nav">
    <div class="container nav-inner">
      <a href="{{ route('face') }}">Chăm sóc da mặt</a>
      <a href="{{ route('hair') }}">Chăm sóc tóc</a>
      <a href="{{ route('body') }}">Chăm sóc cơ thể</a>
      <a href="{{ route('makeup') }}">Trang điểm</a>
      <a href="{{ route('hotdeal') }}">Khuyến mãi</a>
      <a href="{{ route('brands') }}">Thương hiệu</a>
    </div>
  </nav>

  {{-- Flash message --}}
  @if (session('success'))
    <div class="container mt-3">
      <div class="alert alert-success mb-0">{{ session('success') }}</div>
    </div>
  @endif

  <main class="container">@yield('content')</main>

  <footer class="footer mt-4">
    <div class="container">© {{ date('Y') }} Comestic - Nét đẹp tự nhiên 💕</div>
  </footer>

  <!-- Bootstrap 5 JS (bundle có Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Toastr JS --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  {{-- jQuery --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  {{-- JS xử lý thêm vào giỏ (cart.js) --}}
  <script src="{{ asset('js/cart.js') }}?v={{ filemtime(public_path('js/cart.js')) }}"></script>

  <script src="{{ asset('js/buynow.js') }}"></script>
  <div id="toast" class="toast-banner" role="status" aria-live="polite"></div>
</body>
</html> 
 
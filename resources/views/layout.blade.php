<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'XinhXinhShop')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <link rel="icon" href="{{ asset('images/home/xinhxinhshop.png') }}" type="image/png">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    @vite(['resources/css/app.css','resources/js/app.js'])

    @yield('styles')
</head>

<body class="{{ auth()->check() ? 'logged-in' : '' }}">

@php
    $now = now();

    $headerPromotions = \App\Models\Promotion::query()
        ->where('is_active', true)
        ->whereDate('start_date', '<=', $now)
        ->whereDate('end_date', '>=', $now)
        ->orderBy('start_date')
        ->take(10)
        ->get();

    $cartKey = auth()->check()
        ? 'cart_user_' . auth()->id()
        : 'cart_guest';

    $cart = session()->get($cartKey, []);
    $cartCount = collect($cart)->sum(fn($item) => (int) ($item['quantity'] ?? 0));
@endphp

    {{-- TOPBAR --}}
    <div class="topbar">
        <div class="container">
            <div class="promo-ticker">
                <div class="promo-track">
                    @forelse ($headerPromotions as $index => $promo)
                        <span>
                            @switch($index % 4)
                                @case(0) 🌸 @break
                                @case(1) ✨ @break
                                @case(2) 💄 @break
                                @default 🎁
                            @endswitch
                            {{ $promo->name }}
                        </span>
                    @empty
                        <span>🌸 Chào mừng bạn đến với XinhXinhShop</span>
                        <span>✨ Mỹ phẩm chính hãng - Giá siêu xinh</span>
                        <span>💄 Nhiều ưu đãi hấp dẫn mỗi ngày</span>
                        <span>🎁 Theo dõi ngay để không bỏ lỡ khuyến mãi</span>
                    @endforelse

                    @foreach ($headerPromotions as $index => $promo)
                        <span>
                            @switch($index % 4)
                                @case(0) 🌸 @break
                                @case(1) ✨ @break
                                @case(2) 💄 @break
                                @default 🎁
                            @endswitch
                            {{ $promo->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <header class="site-header">
        <div class="container">
            <div class="row g-3 align-items-center">

                {{-- Logo --}}
                <div class="col-12 col-md-2 text-center text-md-start">
                    <a href="{{ url('/') }}" class="logo-link d-inline-flex align-items-center text-decoration-none">
                        <img src="{{ asset('images/home/xinhxinhshop.png') }}" class="logo-img" alt="Logo XinhXinhShop">
                    </a>
                </div>

                {{-- Search --}}
                <div class="col-12 col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2 search">
                        <input
                            type="text"
                            name="q"
                            class="form-control form-control-lg"
                            placeholder="Tìm son, sữa rửa mặt, kem chống nắng..."
                            value="{{ request('q') }}"
                        >
                        <button type="submit" class="btn btn-pink btn-lg fw-bold px-4 text-nowrap">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                {{-- Actions --}}
                <div class="col-12 col-md-4">
                    <div class="header-actions d-flex align-items-center justify-content-center justify-content-md-end gap-2 flex-wrap">

                        <a href="{{ url('/contact') }}" class="btn btn-outline-pink btn-lg fw-bold">
                            Liên hệ
                        </a>

                        @auth
                            <div class="dropdown-custom position-relative">
                                <button
                                    class="btn btn-pink btn-lg fw-bold"
                                    type="button"
                                    id="profileDropdownBtn">
                                    {{ \Illuminate\Support\Str::limit(Auth::user()->name, 14) }}
                                </button>

                                <ul class="account-menu-custom shadow-sm d-none" id="profileDropdownMenu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.info') }}">
                                            <i class="fa-regular fa-user me-2"></i>Tài khoản của tôi
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="dropdown-custom position-relative">
                                <button
                                    class="btn btn-pink btn-lg fw-bold"
                                    type="button"
                                    id="accountDropdownBtn">
                                    Tài khoản
                                </button>

                                <ul class="account-menu-custom shadow-sm d-none" id="accountDropdownMenu">
                                    <li>
                                        <a class="dropdown-item fw-semibold" href="{{ route('login') }}">
                                            <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fw-semibold" href="{{ route('register') }}">
                                            <i class="fa-solid fa-user-plus me-2"></i>Đăng ký
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endauth

                        {{-- Cart --}}
                        <a href="{{ route('cart.view') }}" class="cart-icon text-decoration-none" aria-label="Giỏ hàng">
                            <i class="fa fa-shopping-cart"></i>
                            <span
                                id="cartCount"
                                class="cart-count {{ $cartCount == 0 ? 'is-empty' : '' }} {{ $cartCount >= 10 ? 'double' : '' }}"
                                data-count="{{ $cartCount }}"
                                aria-label="Số sản phẩm trong giỏ"
                            >
                                {{ $cartCount }}
                            </span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- NAVBAR --}}
    <nav class="main-nav">
        <div class="container nav-inner">
            <a href="{{ route('face') }}">Chăm sóc da mặt</a>
            <a href="{{ route('hair') }}">Chăm sóc tóc</a>
            <a href="{{ route('body') }}">Chăm sóc cơ thể</a>
            <a href="{{ route('makeup') }}">Trang điểm</a>
            <a href="{{ route('hotdeal') }}">Khuyến mãi</a>
            <a href="{{ route('brands') }}">Thương hiệu</a>
        </div>
    </nav>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="container mt-3">
            <div class="alert custom-alert-success mb-0">
                {{ session('success') }}
            </div>
        </div>
    @endif

   @if(session('error'))
        <div class="alert alert-danger custom-alert alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-exclamation-triangle fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert custom-alert-danger mb-0">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- MAIN --}}
    <main class="site-main">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer mt-4">
        <div class="container">
            <div class="footer-inner">
                © {{ date('Y') }} XinhXinhShop - Nét đẹp tự nhiên 💕
            </div>
        </div>
    </footer>

    {{-- CHAT TOGGLE --}}
    <button id="chat-toggle" type="button" aria-label="Mở hộp chat">💬</button>

    {{-- CHATBOX --}}
    <div id="chatbox">
        <div id="chatbox-header">Hỗ trợ trực tuyến</div>

        <div id="chatbox-faq" class="p-2 border-bottom">
            <div class="small fw-bold mb-2">Câu hỏi nhanh:</div>

            <div class="d-flex flex-wrap gap-2">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary faq-btn"
                    data-question="Sản phẩm này có chính hãng không?">
                    Chính hãng không?
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary faq-btn"
                    data-question="Cửa hàng có miễn phí vận chuyển không?">
                    Miễn phí vận chuyển?
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary faq-btn"
                    data-question="Bao lâu thì nhận được hàng?">
                    Bao lâu nhận hàng?
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary faq-btn"
                    data-question="Cửa hàng có đổi trả không?">
                    Có đổi trả không?
                </button>
            </div>
        </div>

        <div id="chatbox-messages">
            <p><strong>Hỗ trợ:</strong> Xin chào! Bạn cần giúp gì ạ?</p>
        </div>

        <div id="chatbox-input">
            <input type="text" id="chat-input" placeholder="Nhập tin nhắn...">
            <button id="chat-send" type="button">Gửi</button>
        </div>
    </div>

    {{-- BACK TO TOP --}}
    <button
        type="button"
        id="backToTopBtn"
        class="btn btn-pink back-to-top"
        aria-label="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- TOAST --}}
    <div id="toast" class="toast-banner" role="status" aria-live="polite"></div>

    {{-- JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


    {{-- JS của bạn --}}
    <script src="{{ asset('js/cart.js') }}?v={{ file_exists(public_path('js/cart.js')) ? filemtime(public_path('js/cart.js')) : time() }}"></script>
    <script src="{{ asset('js/buynow.js') }}?v={{ file_exists(public_path('js/buynow.js')) ? filemtime(public_path('js/buynow.js')) : time() }}"></script>
    <script src="{{ asset('js/chatbox.js') }}?v={{ file_exists(public_path('js/chatbox.js')) ? filemtime(public_path('js/chatbox.js')) : time() }}"></script>
    <script src="{{ asset('js/top.js') }}?v={{ file_exists(public_path('js/top.js')) ? filemtime(public_path('js/top.js')) : time() }}"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileBtn = document.getElementById('profileDropdownBtn');
            const profileMenu = document.getElementById('profileDropdownMenu');
            const accountBtn = document.getElementById('accountDropdownBtn');
            const accountMenu = document.getElementById('accountDropdownMenu');

            function closeAllMenus() {
                if (profileMenu) profileMenu.classList.add('d-none');
                if (accountMenu) accountMenu.classList.add('d-none');
            }

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (accountMenu) accountMenu.classList.add('d-none');
                    profileMenu.classList.toggle('d-none');
                });

                profileMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            if (accountBtn && accountMenu) {
                accountBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (profileMenu) profileMenu.classList.add('d-none');
                    accountMenu.classList.toggle('d-none');
                });

                accountMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            document.addEventListener('click', function () {
                closeAllMenus();
            });
        });
    </script>
</body>
</html>
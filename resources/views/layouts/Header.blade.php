<!-- Header Top -->
<div class="header-top text-center">
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img class="logo-img" src="{{ asset('images/home/xinhxinhshop.png') }}" alt="Logo"> 
        </a>
        <h1>XinhXinhShop</h1>
    </div>
    <img class="img-deal" src="{{ asset('images/home/Mypham.png') }}" alt="Deal">
    <img src="{{ asset('images/hot.gif') }}" alt="Hot" style="position: absolute; top: 30px; right: 440px; width: 100px;">
        <div>
            <img class="logo-ship" src="{{ asset('images/home/freeship.png') }}" alt="Freeship"> 
            <h3>FreeShip Toàn Quốc</h3>
        </div>
     

    </div>
</div>

<!-- Header Main -->
<div class="header-main">
    <div class="container mx-auto flex items-center justify-between gap-4 flex-wrap">
        <!-- Logo -->
        <div class="d-flex align-items-center">
            <a href="{{ route('home') }}">
                <img class="logo-mini" src="{{ asset('images/home/xinhxinhshop.png') }}" alt="Logo">
            </a>
            <div class="ms-2">Ưu tiên chất lượng hàng đầu!</div>
        </div>

        <!-- Search Box -->
        <div class="search-box input-group mx-3">
            <input type="text" class="form-control" placeholder="Tìm sản phẩm, thương hiệu bạn mong muốn...">
            <button class="btn btn-light"><i class="bi bi-search"></i></button>
        </div>



        <!-- Icons -->
        <div class="d-flex align-items-center">
            <div class="account-dropdown-wrapper" style="position: relative; display: inline-block;">
                @auth
                    <button class="account-btn">
                        <i class="fa fa-user"></i>{{ Auth::user()->name }}
                    </button>
                    <div class="account-popup">
                        <a href="{{ route('account.info') }}">
                            <i class="fa fa-user"></i> Tài khoản của bạn
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-danger" style="background: none; border: none; padding: 0; margin: 5px 0;">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <button class="account-btn">
                        <i class="fa fa-user"></i> Tài khoản
                    </button>
                    <div class="account-popup">
                        <a href="{{ route('login') }}">Đăng nhập</a>
                        <a href="{{ route('register') }}">Đăng ký</a>
                    </div>
                @endauth
            </div>

            <a href="{{ route('store') }}" class="text-white me-3" style="text-decoration: none;">
                <i class="bi bi-shop me-1"></i> Cửa hàng
            </a>
            <div class="text-white me-3">
                <a href="{{ route('warranty') }}" class="text-white me-3" style="text-decoration: none;">
                    <i class="bi bi-shield-check me-1"></i>Bảo hành
                </a>
            </div>
            <div class class="text-white me-3">
                <a href="{{ route('support') }}"class="text-white me-3" style="text-decoration: none;">
                    <i class="bi bi-telephone me-1"></i> Hỗ trợ
                </a>
            </div>
            <a href="{{ route('cart') }}" id="cartIcon" style="text-decoration: none; color: inherit;">🛒 
                <span id="cartCount">
                    {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                </span>
            </a>


        </div>
    </div>
</div>

<!-- Header Bottom -->


<div class="header-bottom">
    <div class="menu d-flex flex-wrap align-items-center">
        <div class="menu">
            <a href="{{ route('face') }}">
                <i class="menu"></i>CHĂM SÓC MẶT
            </a>
            </div>
        <div class="menu">
            <a href="{{ route('hair') }}">
                <i class="menu"></i>CHĂM SÓC TÓC
            </a>    
        </div>
        <div class="menu">
            <a href="{{ route('body') }}">
                <i class="menu"></i>CHĂM SÓC BODY
            </a>    
        </div>
         <div class="menu">
            <a href="{{ route('makeup') }}">
                <i class="menu"></i>TRANG ĐIỂM
            </a>    
        </div>
        <div class="menu">
            <a href="{{ route('hotdeal') }}"   >
                <i class="menu"></i>HOT DEALS
            </a>
        </div>
        <div class="menu">
            <a href="{{ route('brands') }}">
                <i class="menu"></i> THƯƠNG HIỆU
            </a>
        </div>
    </div>
</div>

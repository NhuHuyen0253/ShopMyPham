<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('scripts')
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>XinhXinhShop</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    @vite('resources/css/style.css') 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @include('layouts.header')
    
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <div class="container mt-4">
        @yield('content')
    </div>
    <button id="backToTop" title="Lên đầu trang">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
        </svg>
    </button>
    <script>
        window.onscroll = function () {
            const btn = document.getElementById("backToTop");
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                btn.style.display = "flex"; // Flex để căn giữa icon
            } else {
                btn.style.display = "none";
            }
        };

        document.getElementById("backToTop").onclick = function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
    <!--thông báo-->
    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <!-- Nút Chat -->
    <button id="chatToggleBtn" title="Chat với chúng tôi">
        💬
    </button>

    <!-- Hộp thoại Chat -->
    <div id="chatBox">
        <div class="chat-header">
            <span>Hỗ trợ trực tuyến</span>
            <button id="closeChat">×</button>
        </div>
        <div class="chat-body">
            <p>Xin chào! Bạn cần hỗ trợ gì không?</p>
            <!-- Thêm nội dung chat ở đây -->
        </div>
        <div class="chat-footer">
            <input type="text" placeholder="Nhập tin nhắn..." />
            <button>Gửi</button>
        </div>
    </div>
    <script>
        const chatBtn = document.getElementById("chatToggleBtn");
        const chatBox = document.getElementById("chatBox");
        const closeChat = document.getElementById("closeChat");

        chatBtn.onclick = function () {
            chatBox.style.display = "flex"; // hoặc "block"
        };

        closeChat.onclick = function () {
            chatBox.style.display = "none";
        };
    </script>

    <script src="{{ asset('js/buynow.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>



</body>
<footer>
    @include('layouts.footer')
</footer>
</html>

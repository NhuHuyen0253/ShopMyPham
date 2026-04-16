<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="forgot-page min-h-screen flex items-center justify-center px-4 py-8">

    <div class="forgot-wrapper w-full max-w-6xl overflow-hidden grid grid-cols-1 md:grid-cols-2 rounded-[28px]">
        
        <!-- Bên trái -->
        <div class="forgot-left hidden md:block">
            <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop">
            <div class="forgot-left-overlay"></div>
        </div>

        <!-- Bên phải -->
        <div class="forgot-form-box w-full px-6 py-10 md:px-10 md:py-12 flex items-center">
            <div class="w-full">
                <div class="text-center mb-8">
                    <h1 class="forgot-title text-3xl font-bold">Quên mật khẩu</h1>
                </div>

                @if (session('status'))
                    <div class="forgot-success-box p-4 mb-5 text-sm">
                        <i class="fa-solid fa-circle-check mr-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="forgot-error-box p-4 mb-5">
                        <ul class="text-sm list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-max font-semibold text-gray-700 mb-2">
                            Email
                        </label>

                        <div class="forgot-input-group">
                            <i class="fa-solid fa-envelope"></i>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="forgot-input"
                                placeholder="Nhập email của bạn"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="forgot-btn w-full text-white font-bold py-3 px-4">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Gửi link đặt lại mật khẩu
                    </button>

                    <p class="mt-4 text-center text-max text-gray-600">
                        Nhớ mật khẩu rồi?
                        <a href="{{ route('login') }}" class="forgot-link font-semibold">
                            Đăng nhập
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
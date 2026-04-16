<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Tài Khoản</title>
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page min-h-screen flex items-center justify-center px-4 py-8">

    <div class="login-wrapper bg-white rounded-[28px] overflow-hidden w-full max-w-6xl grid grid-cols-1 md:grid-cols-2">

        <div class="left-panel hidden md:block">
            <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop">
            <div class="left-overlay"></div>
        </div>

        <div class="form-box w-full px-6 py-10 md:px-10 md:py-12 flex items-center">
            <div class="w-full">
                <div class="text-center mb-8">
                    <h1 class="form-title text-4xl font-bold">Đăng Nhập</h1>
                </div>

                @if ($errors->any())
                    <div class="error-box p-4 mb-5">
                        <ul class="text-sm list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Số điện thoại
                        </label>
                        <div class="input-group-custom">
                            <i class="fa-solid fa-phone"></i>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                class="input-custom"
                                placeholder="Nhập số điện thoại"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mật khẩu
                        </label>
                        <div class="input-group-custom">
                            <i class="fa-solid fa-lock"></i>
                            <input
                                type="password"
                                name="password"
                                class="input-custom"
                                placeholder="Nhập mật khẩu"
                                required
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-gray-600">
                            <input type="checkbox" class="rounded border-pink-300 text-pink-500 focus:ring-pink-400">
                            Ghi nhớ đăng nhập
                        </label>

                        <a href="{{ route('password.request') }}" class="forgot-link font-medium">
                            Quên mật khẩu?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="btn-login w-full text-white font-bold py-3 px-4"
                    >
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>
                        Đăng Nhập
                    </button>

                    <div class="divider-wrap relative text-center pt-2">
                        <span class="divider-text px-3 text-sm relative z-10">hoặc</span>
                        <div class="divider-line absolute left-0 right-0 top-1/2 -z-0"></div>
                    </div>

                    <p class="text-center text-sm text-gray-600 pt-1">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}" class="register-link font-semibold">
                            Đăng ký ngay
                        </a>
                    </p>
                </form>
            </div>
        </div>

    </div>

</body>
</html>
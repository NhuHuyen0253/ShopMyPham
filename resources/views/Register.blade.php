<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="register-page min-h-screen flex items-center justify-center px-4 py-8">

    <div class="register-wrapper w-full max-w-7xl overflow-hidden grid grid-cols-1 md:grid-cols-2 rounded-[28px]">

        <div class="register-left hidden md:block">
            <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop">
            <div class="register-left-overlay"></div>   
        </div>

        <div class="register-form-box w-full px-6 py-10 md:px-10 md:py-12 flex items-center">
            <div class="w-full">
                <div class="text-center mb-8">
                    <h1 class="register-title text-3xl font-bold">Tạo Tài Khoản Mới</h1>
                </div>

                @if ($errors->any())
                    <div class="register-error-box p-4 mb-5">
                        <ul class="text-sm list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Họ tên
                        </label>
                        <div class="register-input-group">
                            <i class="fa-solid fa-user"></i>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                class="register-input"
                                placeholder="Nhập họ tên của bạn"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <div class="register-input-group">
                            <i class="fa-solid fa-envelope"></i>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="register-input"
                                placeholder="Nhập email của bạn"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Số điện thoại
                        </label>
                        <div class="register-input-group">
                            <i class="fa-solid fa-phone"></i>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                class="register-input"
                                placeholder="Nhập số điện thoại"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mật khẩu
                        </label>
                        <div class="register-input-group">
                            <i class="fa-solid fa-lock"></i>
                            <input
                                type="password"
                                name="password"
                                class="register-input"
                                placeholder="Nhập mật khẩu"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nhập lại mật khẩu
                        </label>
                        <div class="register-input-group">
                            <i class="fa-solid fa-shield-heart"></i>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="register-input"
                                placeholder="Nhập lại mật khẩu"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="register-btn w-full text-white font-bold py-3 px-4">
                        <i class="fa-solid fa-user-plus mr-2"></i>
                        Đăng ký
                    </button>

                    <p class="mt-4 text-center text-sm text-gray-600">
                        Đã có tài khoản?
                        <a href="{{ route('login.form') }}" class="register-link font-semibold">
                            Đăng nhập
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
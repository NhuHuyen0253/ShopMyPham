<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="reset-page min-h-screen flex items-center justify-center px-4 py-8">

    <div class="reset-wrapper w-full max-w-6xl overflow-hidden grid grid-cols-1 md:grid-cols-2 rounded-[28px]">

        <div class="reset-left hidden md:block">
            <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop">
            <div class="reset-left-overlay"></div>
        </div>

        <div class="reset-form-box w-full px-6 py-10 md:px-10 md:py-12 flex items-center">
            <div class="w-full">
                <div class="text-center mb-8">
                    <div class="reset-logo-box w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center shadow-sm">
                        <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="Logo" class="w-12 h-12 object-contain">
                    </div>
                    <h1 class="reset-title text-3xl font-bold">Đặt lại mật khẩu</h1>
                </div>

                @if ($errors->any())
                    <div class="reset-error-box p-4 mb-5">
                        <ul class="text-sm list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <div class="reset-input-group">
                            <i class="fa-solid fa-envelope"></i>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $email ?? '') }}"
                                class="reset-input"
                                placeholder="Nhập email của bạn"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mật khẩu mới
                        </label>
                        <div class="reset-input-group">
                            <i class="fa-solid fa-lock"></i>
                            <input
                                type="password"
                                name="password"
                                class="reset-input"
                                placeholder="Nhập mật khẩu mới"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Xác nhận mật khẩu
                        </label>
                        <div class="reset-input-group">
                            <i class="fa-solid fa-shield-heart"></i>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="reset-input"
                                placeholder="Nhập lại mật khẩu mới"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="reset-btn w-full text-white font-bold py-3 px-4">
                        <i class="fa-solid fa-key mr-2"></i>
                        Cập nhật mật khẩu
                    </button>

                    <p class="mt-4 text-center text-max text-gray-600">
                        Quay lại
                        <a href="{{ route('login.form') }}" class="reset-link font-semibold">
                            Đăng nhập
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
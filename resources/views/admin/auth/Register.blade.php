<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>Đăng ký Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin.css'])
</head>
<body>
    <div class="login-shell flex items-center justify-center px-4 py-8">
        <div class="login-card w-full max-w-5xl rounded-3xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
            {{-- Cột trái --}}
            <div class="login-left hidden md:flex flex-col justify-center items-center text-white p-10 relative">
                <div class="brand-logo-wrap rounded-full flex items-center justify-center mb-6 relative z-10">
                    <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop">
                </div>

                <h1 class="text-3xl font-bold mb-3 relative z-10">XinhXinhShop Admin</h1>
            </div>

            {{-- Cột phải --}}
            <div class="w-full p-7 md:p-10 bg-white">
                <div class="max-w-md mx-auto">
                    <div class="text-center mb-8">
                        <div class="md:hidden flex justify-center mb-4">
                            <img src="{{ asset('images/home/xinhxinhshop.png') }}"
                                 alt="XinhXinhShop"
                                 class="w-16 h-16 object-contain rounded-full shadow">
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Đăng ký Admin</h2>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 mb-6 rounded-2xl">
                            <div class="font-semibold mb-1">Đăng ký không thành công</div>
                            <ul class="text-sm list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.register') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">
                                Số điện thoại
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-phone-alt"></i>
                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="form-input w-full px-4 py-3"
                                    placeholder="Nhập số điện thoại"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">
                                Tên người dùng
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-user"></i>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-input w-full px-4 py-3"
                                    placeholder="Nhập tên người dùng"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">
                                Email
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-envelope"></i>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-input w-full px-4 py-3"
                                    placeholder="Nhập email"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">
                                Mật khẩu
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-lock"></i>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-input w-full px-4 py-3"
                                    placeholder="Nhập mật khẩu"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">
                                Xác nhận mật khẩu
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-shield-alt"></i>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-input w-full px-4 py-3"
                                    placeholder="Nhập lại mật khẩu"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit"
                                class="login-btn w-full text-white font-bold py-3 px-4">
                            <i class="fas fa-user-plus mr-2"></i>Đăng ký
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Đã có tài khoản?
                            <a href="{{ route('admin.login.form') }}" class="font-semibold sub-link">
                                Đăng nhập
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
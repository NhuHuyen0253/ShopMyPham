<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>Quên mật khẩu Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin.css'])
</head>
<body class="min-h-screen bg-pink-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
            
            <div class="hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-pink-500 via-rose-400 to-pink-300 text-white p-10">
                <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="XinhXinhShop" class="w-24 h-24 rounded-full shadow-lg mb-6 bg-white p-2">
                <h1 class="text-3xl font-bold mb-3">Khôi phục mật khẩu</h1>
            </div>

            <div class="p-8 md:p-10">
                <div class="max-w-md mx-auto">
                    <div class="text-center mb-8">
                         <div class="md:hidden flex justify-center mb-4">
                            <img src="{{ asset('images/home/xinhxinhshop.png') }}"
                                 alt="XinhXinhShop"
                                 class="w-16 h-16 object-contain rounded-full shadow">
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Quên mật khẩu Admin</h2>
                        <p class="text-gray-500 mt-2">Nhập email để nhận liên kết đặt lại mật khẩu</p>
                    </div>

                    @if(session('status'))
                        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Admin</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-pink-500">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-2xl border border-pink-200 py-3 pl-12 pr-4 outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                    placeholder="Nhập email quản trị"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                Gửi link đặt lại mật khẩu
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="{{ route('admin.login.form') }}" class="font-semibold text-pink-600 hover:text-pink-700">
                            <i class="fas fa-arrow-left mr-1"></i>Quay lại đăng nhập Admin
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
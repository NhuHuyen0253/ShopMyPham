<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>Đăng Ký Tài Khoản</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded shadow-md flex flex-col md:flex-row w-full max-w-4xl overflow-hidden">
        <!-- Hình ảnh bên trái -->
        <div class="hidden md:block md:w-1/2">
            <img src="{{ asset('images/home/xinhxinhshop.png') }}" alt="Hình đăng ký"
                 class="w-full h-full object-cover">
        </div>

        <!-- Form đăng ký bên phải -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6 text-center text-pink-600">Tạo Tài Khoản Mới</h2>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                    <ul class="text-sm list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                    <input type="text" name="name" class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" name="phone" class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input type="password" name="password" class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>

                <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Đăng ký
                </button>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}" class="text-pink-600 hover:underline">Đăng nhập</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>

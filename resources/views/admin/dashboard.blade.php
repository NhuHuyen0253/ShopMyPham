<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen hidden md:block">
        <div class="p-6 border-b">
            <h1 class="text-xl font-bold text-pink-600">ADMIN</h1>
            <img class="img-deal" src="{{ asset('images/home/xinhxinhshop.png') }}" alt="Deal">
        </div>
        <nav class="mt-4">
            <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Trang Chủ</a>
            <a href="{{ route('admin.product.index') }}" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Quản lý sản phẩm</a>
            <a href="{{ route('admin.brand.index') }}" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Thương hiệu</a>
            <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Đơn hàng</a>
            <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Khách hàng</a>
            <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Chương trình khuyến mãi</a>
        </nav>
    </aside>

    <!-- Nội dung chính -->
    <div class="flex-1 flex flex-col">
        <!-- Thanh header -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Trang Chủ</h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.password.change') }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded text-sm">
                    Thay đổi mật khẩu
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded text-sm">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </header>


        <!-- Nội dung bên trong -->
        <main class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded shadow text-center">
                    <h3 class="text-lg font-bold text-gray-700">Tổng sản phẩm</h3>
                    <p class="text-2xl text-pink-600 font-semibold mt-2">{{ $totalProducts }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow text-center">
                    <h3 class="text-lg font-bold text-gray-700">Đơn hàng hôm nay</h3>
                    <p class="text-2xl text-pink-600 font-semibold mt-2">{{ $todayOrders }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow text-center">
                    <h3 class="text-lg font-bold text-gray-700">Khách hàng</h3>
                    <p class="text-2xl text-pink-600 font-semibold mt-2">{{ $totalUsers }}</p>
                </div>
            </div>

        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông báo gần đây</h3>
            <ul class="space-y-2">
                @foreach ($newProducts as $product)
                    <li class="bg-white p-3 rounded shadow text-sm text-gray-600">
                        🎉 Sản phẩm mới: <strong>{{ $product->name }}</strong> đã được thêm.
                    </li>
                @endforeach

                @foreach ($newOrders as $order)
                    <li class="bg-white p-3 rounded shadow text-sm text-gray-600">
                        📦 Đơn hàng mới từ khách hàng <strong>{{ $order->user->name ?? 'Khách chưa đăng ký' }}</strong>.
                    </li>
                @endforeach

                @foreach ($lowStock as $product)
                    <li class="bg-white p-3 rounded shadow text-sm text-gray-600">
                        🔔 Sản phẩm <strong>{{ $product->name }}</strong> sắp hết hàng (còn {{ $product->quantity }}).
                    </li>
                @endforeach
            </ul>
        </div>

        </main>
    </div>
</body>
</html>

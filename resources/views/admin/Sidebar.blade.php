<aside class="w-64 bg-white shadow-md hidden md:block">
    <div class="p-6 border-b">
        <h1 class="text-xl font-bold text-pink-600">ADMIN</h1>
    </div>
    <nav class="mt-4">
        <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 hover:bg-pink-50">Trang chủ</a>
        <a href="{{ route('admin.product.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Quản lý sản phẩm</a>
        <a href="{{ route('admin.brand.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Thương hiệu</a>
        <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Đơn hàng</a>
        <a href="#" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Khách hàng</a>
    </nav>
</aside>

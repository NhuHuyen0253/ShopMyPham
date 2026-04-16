<aside class="w-64 bg-white shadow-md hidden md:block">
    <div class="p-6 border-b">
        <h1 class="text-xl font-bold text-pink-600">ADMIN</h1>
        <img
        src="{{ asset('images/home/xinhxinhshop.png') }}"
        alt="Ảnh đại diện shop"
        class="mt-2 w-25 h-25 rounded-none ring-2 ring-gray-200"
    >
    </div>
    <nav class="mt-4">
        <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 hover:bg-pink-50">Trang chủ</a>
        <a href="{{ route('admin.product.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Sản phẩm</a>
        <a href="{{ route('admin.category.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Quản lý danh mục</a>
        <a href="{{ route('admin.brand.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Thương hiệu</a>
        <a href="{{ route('admin.orders.index') }}" class="block py-2.5 px-4 hover:bg-pink-50 text-gray-700">Đơn hàng</a>
        <a href="{{ route('admin.customers.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Khách hàng</a>
        <a href="{{ route('admin.employee.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Danh sách nhân viên</a>
        <a href="{{ route('admin.supplier.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Nhà cung cấp</a>
        <a href="{{ route('admin.stock.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Quản lý kho</a>
        <a href="{{ route('admin.revenue.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Quản lý doanh thu</a>
        <a href="{{ route('admin.promotions.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Khuyến mãi</a>
        <a href="{{ route('admin.promo-banners.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Banner khuyến mãi</a>
        <a href="{{ route('admin.reviews.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Đánh giá của khách hàng</a>
        <a href="{{ route('admin.contacts.index') }}" class="block py-2.5 px-4 hover:bg-pink-50">Hỏi đáp của khách hàng</a>

    </nav>
</aside>

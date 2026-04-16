@extends('layout')

@section('title', 'Chính sách bảo mật')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Chính Sách Bảo Mật</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop cam kết bảo vệ thông tin cá nhân của khách hàng, đảm bảo việc thu thập<br>
                            và sử dụng dữ liệu được thực hiện minh bạch, an toàn và đúng mục đích.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Thông tin thu thập</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Họ tên, số điện thoại và địa chỉ nhận hàng.</li>
                            <li class="mb-2">- Địa chỉ email khi khách hàng đăng ký tài khoản hoặc liên hệ.</li>
                            <li>- Lịch sử đơn hàng và thông tin cần thiết để hỗ trợ mua sắm.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Mục đích sử dụng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Xử lý đơn hàng và xác nhận thông tin mua hàng.</li>
                            <li class="mb-2">- Hỗ trợ chăm sóc khách hàng trong quá trình sử dụng dịch vụ.</li>
                            <li>- Gửi thông tin ưu đãi, khuyến mãi hoặc thông báo mới từ cửa hàng.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">3. Cam kết bảo mật</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Không chia sẻ, mua bán hoặc trao đổi thông tin khách hàng cho bên thứ ba nếu không có sự đồng ý.</li>
                            <li>- Thông tin cá nhân của khách hàng được lưu trữ và bảo mật theo quy trình quản lý phù hợp.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 4 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">4. Quyền của khách hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Yêu cầu kiểm tra, chỉnh sửa hoặc xóa thông tin cá nhân đã cung cấp.</li>
                            <li>- Chủ động từ chối nhận email quảng cáo hoặc thông tin tiếp thị từ cửa hàng.</li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('contact.contact') }}" class="btn btn-secondary">
                    ← Quay lại 
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
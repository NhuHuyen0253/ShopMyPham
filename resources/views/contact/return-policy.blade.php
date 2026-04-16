@extends('layout')

@section('title', 'Chính sách đổi trả')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Chính Sách Đổi Trả</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop luôn đặt quyền lợi khách hàng lên hàng đầu. Chúng tôi hỗ trợ đổi trả minh bạch,<br>
                            nhanh chóng trong trường hợp sản phẩm gặp lỗi hoặc không đúng mô tả.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Điều kiện đổi trả</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Sản phẩm bị lỗi từ nhà sản xuất.</li>
                            <li class="mb-2">- Sản phẩm bị hư hỏng trong quá trình vận chuyển.</li>
                            <li class="mb-2">- Sản phẩm giao sai mẫu, sai số lượng.</li>
                            <li class="mb-2">- Sản phẩm chưa sử dụng, còn nguyên tem, hộp.</li>
                            <li>- Thời gian đổi trả: trong vòng <strong>7 ngày</strong>.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Không áp dụng đổi trả</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Sản phẩm đã mở nắp hoặc sử dụng.</li>
                            <li class="mb-2">- Hư hỏng do người dùng.</li>
                            <li class="mb-2">- Không còn bao bì, tem nhãn ban đầu.</li>
                            <li>- Sản phẩm khuyến mãi, giảm giá nhưng không có lỗi.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">3. Quy trình đổi trả</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">Bước 1: Liên hệ hotline <strong>0384528393</strong>.</li>
                            <li class="mb-2">Bước 2: Cung cấp hình ảnh hoặc video sản phẩm gặp vấn đề.</li>
                            <li class="mb-2">Bước 3: Gửi sản phẩm về shop theo hướng dẫn của nhân viên hỗ trợ.</li>
                            <li>Bước 4: Shop tiếp nhận và xử lý trong vòng <strong>2 - 3 ngày làm việc</strong>.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 4 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">4. Chi phí đổi trả</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Nếu lỗi phát sinh từ shop hoặc nhà sản xuất, shop sẽ chịu toàn bộ chi phí đổi trả.</li>
                            <li>- Nếu khách hàng muốn đổi sản phẩm theo nhu cầu cá nhân, khách hàng sẽ thanh toán chi phí vận chuyển phát sinh.</li>
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
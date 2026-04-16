@extends('layout')

@section('title', 'Tuyển dụng')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Tuyển Dụng</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop luôn chào đón những ứng viên năng động, thân thiện và yêu thích lĩnh vực làm đẹp<br>
                            cùng đồng hành để mang đến trải nghiệm mua sắm tốt nhất cho khách hàng.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Vị trí tuyển dụng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Nhân viên bán hàng: <strong>02 người</strong>.</li>
                            <li class="mb-2">- Làm việc theo hình thức <strong>Full-time</strong> hoặc <strong>Part-time</strong>.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Mô tả công việc</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Tư vấn sản phẩm và hỗ trợ khách hàng trong quá trình mua sắm.</li>
                            <li class="mb-2">- Sắp xếp, trưng bày và kiểm tra hàng hóa tại cửa hàng.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">3. Quyền lợi</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Mức lương từ <strong>5 - 7 triệu</strong> đồng/tháng và thưởng theo hiệu quả làm việc.</li>
                            <li class="mb-2">- Môi trường làm việc thân thiện, năng động và có cơ hội học hỏi thêm về lĩnh vực mỹ phẩm.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 4 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">4. Thông tin ứng tuyển</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2"><strong>- Email:</strong> XinhXinhShop@gmail.com</li>
                            <li class="mb-2"><strong>- Số điện thoại:</strong> 0384528393</li>
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
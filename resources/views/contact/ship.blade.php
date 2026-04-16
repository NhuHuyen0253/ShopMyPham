@extends('layout')

@section('title', 'Chính sách vận chuyển')

@section('content')
<div class="container py-4 py-md-5 policy-page">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4 mb-md-5">
                        <h1 class="fw-bold text-dark mb-3">Chính Sách Vận Chuyển</h1>
                        <p class="text-muted mb-0">
                            XinhXinhShop cam kết mang đến dịch vụ vận chuyển nhanh chóng, an toàn và minh bạch
                            cho khách hàng. Chúng tôi luôn cố gắng đảm bảo sản phẩm đến tay khách hàng
                            đúng thời gian và trong tình trạng tốt nhất.
                        </p>
                    </div>

                    {{-- Mục 1 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">1. Phạm vi áp dụng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Áp dụng cho tất cả đơn hàng đặt tại website <strong>XinhXinhShop</strong>.</li>
                            <li class="mb-2">- Đơn vị vận chuyển chính: <strong>Giao Hàng Nhanh (GHN)</strong>.</li>
                            <li class="mb-2">- Hỗ trợ giao hàng trên toàn quốc.</li>
                            <li class="mb-2">- Hiện chưa áp dụng giao hàng ra nước ngoài.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 2 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">2. Phí vận chuyển</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Phí vận chuyển cố định: <strong>30.000đ/đơn hàng</strong>.</li>
                            <li class="mb-2">- Chi phí vận chuyển sẽ được hiển thị rõ ràng tại bước thanh toán.</li>
                            <li class="mb-2">- Không phát sinh thêm phí sau khi khách hàng đặt hàng thành công.</li>
                            <li class="mb-2">- Trong một số chương trình khuyến mãi, shop có thể hỗ trợ miễn phí vận chuyển.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 3 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">3. Thời gian giao hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khu vực nội thành: từ <strong>1 - 2 ngày làm việc</strong>.</li>
                            <li class="mb-2">- Các tỉnh/thành khác: từ <strong>2 - 4 ngày làm việc</strong>.</li>
                            <li class="mb-2">- Thời gian giao hàng có thể thay đổi do thời tiết, lễ Tết hoặc các yếu tố khách quan khác.</li>
                            <li class="mb-2" >- Đối với đơn hàng đặt sau <strong>17:00</strong>, thời gian xử lý sẽ được tính từ ngày làm việc tiếp theo.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 4 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">4. Quy trình giao hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Sau khi đặt hàng thành công, shop sẽ xác nhận thông tin qua điện thoại hoặc tin nhắn.</li>
                            <li class="mb-2">- Đơn hàng sẽ được đóng gói và xử lý trong vòng <strong>24 giờ</strong>.</li>
                            <li class="mb-2">- Sau đó, đơn hàng được bàn giao cho GHN và mã vận đơn sẽ được gửi đến khách hàng.</li>
                            <li class="mb-2">- Khách hàng có thể theo dõi quá trình giao hàng thông qua hệ thống của GHN.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 5 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">5. Kiểm tra khi nhận hàng</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khách hàng được kiểm tra tình trạng sản phẩm trước khi thanh toán.</li>
                            <li class="mb-2">- Nếu phát hiện sản phẩm sai hoặc có lỗi, khách hàng có thể từ chối nhận hàng.</li>
                            <li class="mb-2">- Để đảm bảo quyền lợi, vui lòng quay video khi mở hàng nếu có vấn đề phát sinh.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 6 --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-3">6. Trường hợp giao hàng không thành công</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Đơn vị vận chuyển sẽ liên hệ tối đa <strong>3 lần</strong> để giao hàng.</li>
                            <li class="mb-2">- Nếu không thể liên lạc với khách hàng, đơn hàng có thể bị hủy.</li>
                            <li class="mb-2">- Trong một số trường hợp, khách hàng đặt lại đơn có thể được yêu cầu thanh toán trước.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    {{-- Mục 7 --}}
                    <div>
                        <h4 class="fw-bold text-dark mb-3">7. Lưu ý</h4>
                        <ul class="text-muted ps-3 mb-0">
                            <li class="mb-2">- Khách hàng vui lòng cung cấp địa chỉ nhận hàng chính xác để tránh chậm trễ.</li>
                            <li class="mb-2">- Shop không chịu trách nhiệm đối với trường hợp giao hàng chậm hoặc thất lạc do sai thông tin địa chỉ.</li>
                            <li class="mb-2">- Mọi thay đổi liên quan đến chính sách vận chuyển sẽ được cập nhật trên website.</li>
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
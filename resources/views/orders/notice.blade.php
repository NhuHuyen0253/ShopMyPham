@extends('layout')

@section('content')
<div class="container py-5">

  @if(session('success') && $order->payment_method !== 'vnpay')
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="fw-bold mb-2">
        Cảm ơn bạn đã đặt hàng 🎉
      </h4>

      <p class="mb-3">
        Mã đơn: <strong>#{{ $order->id }}</strong>
      </p>

      @php
        $subtotal = $order->orderItems->sum(function ($item) {
            return (int)$item->price * (int)$item->quantity;
        });

        $shippingFee = (int)($order->shipping_fee ?? 0);
        $grandTotal = (int)($order->total ?? ($subtotal + $shippingFee));
      @endphp

      <h5 class="fw-bold mb-3">Chi tiết đơn hàng</h5>

      <ul class="list-unstyled mb-3">
        @foreach($order->orderItems as $item)
          <li class="mb-2">
            Sản phẩm: <strong>{{ $item?->product?->name }}</strong><br>
            Số lượng: <strong>{{ (int)$item->quantity }}</strong><br>
            Thành tiền:
            <strong>
              {{ number_format((int)$item->price * (int)$item->quantity, 0, ',', '.') }} đ
            </strong>
          </li>
        @endforeach
      </ul>

      <hr>

      <h5 class="fw-bold mb-3">Thông tin thanh toán</h5>

      <ul class="list-unstyled mb-3">
        <li>Tạm tính: <strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong></li>
        <li>Phí vận chuyển: <strong>{{ number_format($shippingFee, 0, ',', '.') }} đ</strong></li>
        <li>Tổng thanh toán: <strong class="text-danger">{{ number_format($grandTotal, 0, ',', '.') }} đ</strong></li>
        <li>Phương thức:
          <strong>
            @switch($order->payment_method)
              @case('vnpay') VNPay @break
              @case('cod') COD @break
              @case('bank') Chuyển khoản @break
              @default COD
            @endswitch
          </strong>
        </li>
        <li>Trạng thái:
          <strong>
            @if((int)$order->is_paid === 1)
              Đã thanh toán ✅
            @else
              Chưa thanh toán ⏳
            @endif
          </strong>
        </li>
      </ul>

      <hr>

      <h5 class="fw-bold mb-3">Thông tin nhận hàng</h5>
      <ul class="list-unstyled mb-3">
        <li>Người nhận: <strong>{{ $order->receiver_name }}</strong></li>
        <li>Số điện thoại: <strong>{{ $order->receiver_phone }}</strong></li>
        <li>Địa chỉ: <strong>{{ $order->receiver_addr }}</strong></li>
        <li>Đơn vị vận chuyển: <strong>{{ $order->shipping_carrier ?? 'GHN' }}</strong></li>
        <li>Hình thức giao hàng: <strong>{{ $order->shipping_service ?? 'Giao hàng tiêu chuẩn' }}</strong></li>
      </ul>

      @if($order->payment_method === 'bank')
        <div class="alert alert-info">
          Vui lòng chuyển khoản theo hướng dẫn ở trang trước.
          Sau khi chuyển thành công, hệ thống sẽ xác minh và cập nhật trạng thái đơn hàng.
        </div>
      @elseif($order->payment_method === 'vnpay')
        <div class="alert alert-info">
          Bạn đã chọn thanh toán VNPay.
          @if((int)$order->is_paid === 1)
            Đơn đã được xác nhận thanh toán ✅
          @else
            Hệ thống đang xác nhận giao dịch từ VNPay, vui lòng đợi ít giây và tải lại trang.
          @endif
        </div>
      @else
        <div class="alert alert-info">
          Đơn hàng sẽ được xác nhận và giao sớm nhất. Hãy giữ điện thoại để tài xế liên hệ nhé!
        </div>
      @endif

      <div class="d-flex gap-2">
        <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
      </div>
    </div>
  </div>
</div>
@endsection
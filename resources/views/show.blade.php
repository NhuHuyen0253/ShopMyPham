@extends('layout')

@section('content')
<div class="container py-5">
    <div class="row align-items-start g-5">

    {{-- ================= CỘT TRÁI: GALLERY + MÔ TẢ ================= --}}
    <div class="col-md-5">
      @php
        // Ảnh chính
        $mainImg = $product->image
            ? asset('images/product/'.$product->image)
            : asset('images/placeholder.png');

        // Map gallery -> URL
        $gallery = collect($product->images ?? [])->map(function($i){
            if (is_string($i)) return asset('images/product/' . $i);
            if (is_object($i)) {
                if (isset($i->url)) return $i->url;
                if (isset($i->path, $i->file_name)) return asset(trim($i->path,'/').'/'.$i->file_name);
            }
            return null;
        })->filter()->values();

        // Đưa ảnh chính lên đầu, loại trùng
        $gallery = collect([$mainImg])->merge($gallery)->unique()->values();
      @endphp

      {{-- GALLERY: THUMB DỌC + ẢNH LỚN --}}
      <div class="d-flex gap-3">
        {{-- Thumbnails dọc (desktop) --}}
        <div id="thumbList" class="d-none d-md-flex flex-column gap-3" style="width:90px; max-height:520px; overflow:auto;">
          @foreach($gallery as $i => $src)
            <button type="button"
                    class="thumb btn p-0 border rounded-3 {{ $i===0 ? 'thumb--active border-pink' : '' }}"
                    data-src="{{ $src }}"
                    style="width:90px; height:90px; overflow:hidden;">
              <img src="{{ $src }}" alt="thumb {{ $i }}" style="width:100%; height:100%; object-fit:cover;">
            </button>
          @endforeach
        </div>

        {{-- Ảnh lớn --}}
        <div class="flex-grow-1">
          <div class="bg-white rounded-4 shadow p-3">
            <img id="mainImg"
                 src="{{ $gallery[0] ?? $mainImg }}"
                 alt="{{ $product->name }}"
                 class="img-fluid w-100 rounded-4"
                 style="max-height:520px; object-fit:contain;">
          </div>
        </div>
      </div>

      {{-- Thumbnails ngang (mobile) --}}
      @if($gallery->count() > 1)
        <div class="d-md-none mt-3">
          <div class="d-flex gap-2 overflow-auto">
            @foreach($gallery as $idx => $src)
              <button type="button"
                      class="thumb btn p-0 border rounded-3 {{ $idx===0 ? 'thumb--active border-pink' : '' }}"
                      data-src="{{ $src }}"
                      style="width:76px; height:76px; overflow:hidden;">
                <img src="{{ $src }}" alt="thumb {{ $idx }}" style="width:100%; height:100%; object-fit:cover;">
              </button>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Mô tả & Hướng dẫn --}}
      <div class="mt-4">
        @if(!empty($product->description))
          <h5 class="fw-semibold text-pink">Mô tả sản phẩm</h5>
          <div class="bg-white border rounded p-3">{!! nl2br(e($product->description)) !!}</div>
        @endif

        @if(!empty($product->usage_instructions))
          <h5 class="fw-semibold text-pink mt-4">Hướng dẫn sử dụng</h5>
          <div class="bg-light border rounded p-3">{!! nl2br(e($product->usage_instructions)) !!}</div>
        @endif
      </div>
    </div>

    {{-- ================= CỘT PHẢI: THÔNG TIN MUA HÀNG ================= --}}
    <div class="col-md-7">
      <h3 class="fw-bold text-dark">{{ $product->name }}</h3>

      @php $price = $product->price; @endphp
      <div class="mt-2 mb-3">
        <span class="price fs-4">
          {{ $price !== null ? number_format($price, 0, ',', '.') . ' đ' : 'Liên hệ' }}
        </span>
      </div>

      <div class="d-flex flex-wrap gap-3 mt-3">
        <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart d-flex gap-2 align-items-center">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:110px">
          <button type="submit" class="btn btn-success px-4">Thêm vào giỏ hàng</button>
        </form>

        <button class="btn btn-pink px-5" data-bs-toggle="modal" data-bs-target="#buyNowModal">
          Mua ngay
        </button>
      </div>

      <hr class="my-4">
    </div>
  </div>

  {{-- Đánh giá --}}
  <div class="mt-5">
    <h5 class="fw-bold">Đánh giá sản phẩm</h5>
    <div class="border rounded p-3 bg-white shadow-sm">
      <span class="text-warning">★★★★★</span>
      <small>(5/5 từ 123 đánh giá)</small>
    </div>
  </div>
</div>
@include('orders.buynow')
<script src="{{ asset('js/detail.js') }}" defer></script>
@endsection
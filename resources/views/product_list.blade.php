<div class="row">
    @foreach ($products as $product)
        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="{{ asset('images/product/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ $product->short_description }}</p>
                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary">Xem chi tiết</a>
                </div>
            </div>
        </div>
    @endforeach
</div> 

<!-- Phân trang sản phẩm -->
<div class="d-flex justify-content-center">
    {{ $products->links('vendor.pagination.custom') }} <!-- Phân trang -->
</div>

@extends('admin.layout')
@section('content')
<div class="p-6 max-w-xl">
  <h2 class="text-xl font-bold mb-4">Xuất kho</h2>
  <form method="POST" action="{{ route('admin.inventory.out.store') }}" class="space-y-3">
    @csrf
    <select name="product_id" class="w-full border p-2" required>
      <option value="">-- Sản phẩm --</option>
      @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
    </select>
    <select name="warehouse_id" class="w-full border p-2" required>
      @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
    </select>
    <input type="number" name="quantity" class="w-full border p-2" placeholder="Số lượng" min="1" required>
    <input type="text" name="reference_code" class="w-full border p-2" placeholder="Số phiếu / Mã tham chiếu">
    <textarea name="note" class="w-full border p-2" placeholder="Ghi chú"></textarea>
    <button class="px-4 py-2 bg-pink-600 text-white rounded">Lưu xuất kho</button>
  </form>
</div>
@endsection

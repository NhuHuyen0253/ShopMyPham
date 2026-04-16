@extends('admin.layout')

@section('content')
<div class="p-6 text-[15px] md:text-base">
  <h2 class="text-3xl font-bold mb-4">
    @if($type === 'in')
      Danh sách phiếu nhập
    @elseif($type === 'out')
      Danh sách phiếu xuất
    @else
      Danh sách phiếu nhập/xuất
    @endif
  </h2>

  {{-- Form lọc --}}
  <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
    @if($type)
      <input type="hidden" name="type" value="{{ $type }}">
    @endif

    <div>
      <label class="block text-xs text-gray-500 mb-1">Từ ngày</label>
      <input type="date" name="from" value="{{ request('from') }}"
             class="border rounded px-2 py-1 text-sm">
    </div>

    <div>
      <label class="block text-xs text-gray-500 mb-1">Đến ngày</label>
      <input type="date" name="to" value="{{ request('to') }}"
             class="border rounded px-2 py-1 text-sm">
    </div>

    <button class="px-3 py-2 border rounded bg-slate-100 text-sm">
      Lọc
    </button>

    <a href="{{ route('admin.inventory.movements.index', ['type' => $type]) }}"
       class="px-3 py-2 text-sm text-blue-600">
      Xóa lọc
    </a>

    <a href="{{ route('admin.inventory.index') }}"
       class="ml-auto text-sm text-gray-500 hover:underline">
      ← Quay lại tổng quan kho
    </a>
  </form>

  {{-- Bảng danh sách --}}
  <div class="overflow-x-auto bg-white rounded-lg border">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-3 py-2 text-left">Loại</th>
          <th class="px-3 py-2 text-left">Sản phẩm</th>
          <th class="px-3 py-2 text-left">Số lượng</th>
          <th class="px-3 py-2 text-left">Kho</th>
          <th class="px-3 py-2 text-left">Nhà cung cấp</th>
          <th class="px-3 py-2 text-left">Ngày giờ</th>
        </tr>
      </thead>
      <tbody>
        @forelse($moves as $m)
          <tr class="border-t">
            <td class="px-3 py-2 whitespace-nowrap">
              <span class="px-2 py-1 rounded border font-medium text-xs
                @if($m->type=='in')
                  bg-green-100 text-green-800 border-green-200
                @else
                  bg-red-100 text-red-800 border-red-200
                @endif">
                {{ $m->type == 'in' ? 'Nhập' : 'Xuất' }}
              </span>
            </td>
            <td class="px-3 py-2">
              {{ $m->product->name ?? '-' }}
            </td>
            <td class="px-3 py-2">
              {{ number_format($m->quantity) }}
            </td>
            <td class="px-3 py-2">
              {{ $m->warehouse->name ?? '-' }}
            </td>
            <td class="px-3 py-2">
              {{ $m->supplier->name ?? '-' }}
            </td>
            <td class="px-3 py-2 whitespace-nowrap">
              {{ $m->moved_at ? $m->moved_at->format('d/m/Y H:i') : '' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-3 py-4 text-center text-gray-500">
              Không có dữ liệu.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $moves->links() }}
  </div>
</div>
@endsection

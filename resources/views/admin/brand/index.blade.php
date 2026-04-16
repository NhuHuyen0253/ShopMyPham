@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Danh sách thương hiệu</h1>
        </div>

        <a href="{{ route('admin.brand.create') }}" class="btn-admin-pink">+ Thêm thương hiệu</a>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[260px]">
                    <label class="admin-label">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="admin-input" placeholder="Tìm kiếm thương hiệu...">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-admin-pink">Tìm kiếm</button>
                    @if(request('q'))
                        <a href="{{ url()->current() }}" class="btn-admin-light">Xoá lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="admin-table-wrap">
        <div class="overflow-x-auto">
            <table class="admin-table text-lg">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên thương hiệu</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td>
                                @if($brand->image)
                                    <img src="{{ asset('images/brand/' . $brand->image) }}" class="h-16 w-24 object-contain rounded-lg bg-white border p-1" alt="{{ $brand->name }}">
                                @else
                                    <span class="text-gray-400 italic text-sm">Chưa có ảnh</span>
                                @endif
                            </td>

                            <td class="font-semibold text-gray-800">{{ $brand->name }}</td>

                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.brand.edit', $brand) }}" class="admin-action-btn edit">Sửa</a>
                                    <form action="{{ route('admin.brand.destroy', $brand) }}" method="POST" onsubmit="return confirm('Xác nhận xoá?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-btn delete">Xoá</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500">Chưa có thương hiệu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $brands->links() }}
    </div>
</div>
@endsection
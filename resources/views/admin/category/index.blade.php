@extends('admin.layout')

@section('content')
<div class="p-6 admin-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="admin-page-title">Quản lý danh mục</h1>
        </div>

        <a href="{{ route('admin.category.create') }}" class="btn-admin-pink">+ Thêm danh mục</a>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.category.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="min-w-[260px] flex-1">
                    <label class="admin-label">Từ khóa</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="admin-input" placeholder="Nhập tên hoặc slug danh mục...">
                </div>

                <div class="w-full md:w-56">
                    <label class="admin-label">Loại</label>
                    <select name="type" class="admin-select">
                        <option value="">-- Tất cả loại --</option>
                        <option value="face" {{ request('type') == 'face' ? 'selected' : '' }}>Face</option>
                        <option value="hair" {{ request('type') == 'hair' ? 'selected' : '' }}>Hair</option>
                        <option value="body" {{ request('type') == 'body' ? 'selected' : '' }}>Body</option>
                        <option value="makeup" {{ request('type') == 'makeup' ? 'selected' : '' }}>Makeup</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-admin-pink">Lọc</button>
                    <a href="{{ route('admin.category.index') }}" class="btn-admin-light">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-table-wrap">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Loại</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>#{{ $category->id }}</td>
                            <td class="font-semibold text-gray-800">{{ $category->name }}</td>
                            <td class="text-gray-500">{{ $category->slug }}</td>
                            <td>
                                <span class="admin-badge admin-badge-pink">{{ $category->type ?: 'Không có' }}</span>
                            </td>
                            <td>
                                <div class="admin-action-group">
                                    <a href="{{ route('admin.category.edit', $category->id) }}" class="admin-action-btn edit">Sửa</a>
                                    <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-btn delete">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">Chưa có danh mục nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
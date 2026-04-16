@extends('admin.layout')

@section('content')
<div class="container py-4 admin-page">
    <div class="admin-page-header mb-4">
        <div>
            <h1 class="admin-page-title">Quản lý hỏi đáp</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tìm kiếm</label>
                    <input
                        type="text"
                        name="q"
                        class="form-control admin-input"
                        value="{{ $q }}"
                        placeholder="Tên, email, tiêu đề, nội dung..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select admin-input">
                        <option value="">-- Tất cả --</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ trả lời</option>
                        <option value="replied" {{ $status === 'replied' ? 'selected' : '' }}>Đã trả lời</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-admin-pink w-100 rounded-pill">
                        <i class="fas fa-filter me-2"></i>Lọc
                    </button>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-light border w-100 rounded-pill">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-table-toolbar">
            <div>
                <div class="admin-table-title">Danh sách câu hỏi</div>
                <div class="admin-table-count">
                    Tổng cộng {{ $contacts->total() }} câu hỏi
                </div>
            </div>
        </div>

        <div class="admin-table-wrap border-0 shadow-none rounded-0">
            <div class="table-responsive">
                <table class="admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="70" class="text-center">#</th>
                            <th>Khách hàng</th>
                            <th>Tiêu đề</th>
                            <th class="text-center">Ngày gửi</th>
                            <th class="text-center">Trạng thái</th>
                            <th width="220" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($contacts as $contact)
                            <tr>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $contact->id }}
                                </td>

                                <td>
                                    <div class="fw-bold text-dark mb-1">{{ $contact->name }}</div>
                                    <div class="text-muted small">{{ $contact->email }}</div>
                                </td>

                                <td>
                                    <div class="fw-semibold text-dark mb-1">
                                        {{ $contact->subject }}
                                    </div>
                                    <div class="text-muted small" style="max-width: 360px; line-height: 1.6;">
                                        {{ \Illuminate\Support\Str::limit($contact->message, 90) }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ $contact->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-center">
                                    @if($contact->status === 'replied')
                                        <span class="admin-badge admin-badge-green">Đã trả lời</span>
                                    @else
                                        <span class="admin-badge admin-badge-yellow">Chờ trả lời</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="admin-action-group">
                                        <a href="{{ route('admin.contacts.show', $contact->id) }}"
                                           class="admin-action-btn edit">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>

                                        <form action="{{ route('admin.contacts.destroy', $contact->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn chắc chắn muốn xóa câu hỏi này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-btn delete">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="empty-contact-icon mb-3">
                                            <i class="fas fa-envelope-open-text"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Chưa có câu hỏi nào</h5>
                                        <p class="text-muted mb-0">
                                            Khi khách hàng gửi liên hệ hoặc câu hỏi, dữ liệu sẽ hiển thị tại đây.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($contacts->hasPages())
            <div class="admin-table-footer d-flex justify-content-end">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
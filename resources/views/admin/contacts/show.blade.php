@extends('admin.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><strong>Chi tiết hỏi đáp #{{ $contact->id }}</strong></h3>
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-light">← Danh sách</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Thông tin câu hỏi</h5>

                    <div class="mb-2">
                        <strong>Khách hàng:</strong> {{ $contact->name }}
                    </div>

                    <div class="mb-2">
                        <strong>Email:</strong> {{ $contact->email }}
                    </div>

                    <div class="mb-2">
                        <strong>Tiêu đề:</strong> {{ $contact->subject }}
                    </div>

                    <div class="mb-2">
                        <strong>Ngày gửi:</strong> {{ $contact->created_at?->format('d/m/Y H:i') }}
                    </div>

                    <div class="mb-3">
                        <strong>Trạng thái:</strong>
                        @if($contact->status === 'replied')
                            <span class="badge bg-success">Đã trả lời</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ trả lời</span>
                        @endif
                    </div>

                    <div>
                        <strong>Nội dung:</strong>
                        <div class="border rounded p-3 mt-2 bg-light">
                            {{ $contact->message }}
                        </div>
                    </div>

                    @if($contact->reply)
                        <div class="mt-4">
                            <strong>Phản hồi hiện tại:</strong>
                            <div class="border rounded p-3 mt-2" style="background: #f8fff8;">
                                {{ $contact->reply }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Cập nhật trạng thái</h5>

                    <form action="{{ route('admin.contacts.status', $contact->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="pending" {{ $contact->status === 'pending' ? 'selected' : '' }}>Chờ trả lời</option>
                                <option value="replied" {{ $contact->status === 'replied' ? 'selected' : '' }}>Đã trả lời</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Lưu trạng thái
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Phản hồi khách hàng</h5>

                    <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nội dung phản hồi</label>
                            <textarea name="reply" rows="7" class="form-control">{{ old('reply', $contact->reply) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Gửi phản hồi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
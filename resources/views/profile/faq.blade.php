@extends('layout')

@section('title', 'Hỏi đáp')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row g-4">

        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                 <div class="card-body p-0">
                   
                    <ul class="list-group list-group-flush account-side-menu">
                        <li class="list-group-item {{ request()->routeIs('profile.info') ? 'active' : '' }}">
                            <a href="{{ route('profile.info') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-user"></i>
                                <span>Thông tin tài khoản</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-box"></i>
                                <span>Đơn hàng của tôi</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-heart"></i>
                                <span>Danh sách yêu thích</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.rebuy') ? 'active' : '' }}">
                            <a href="{{ route('profile.rebuy') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Mua lại</span>
                            </a>
                        </li>

                        <li class="list-group-item {{ request()->routeIs('profile.faq') ? 'active' : '' }}">
                            <a href="{{ route('profile.faq') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-regular fa-circle-question"></i>
                                <span>Hỏi đáp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-4">Hỏi đáp</h3>

                    @if(session('success'))
                        <div class="custom-success-alert mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h5 class="fw-bold mb-3 ">Câu hỏi thường gặp</h5>
                    <div class="faq-list mb-4">
                        @foreach($faqs as $faq)
                            <details class="faq-item">
                                <summary class="faq-question">
                                    <span>{{ $faq['question'] }}</span>
                                    <i class="fas fa-chevron-down faq-icon"></i>
                                </summary>

                                <div class="faq-answer">
                                    {{ $faq['answer'] }}
                                </div>
                            </details>
                        @endforeach
                    </div>

                    <h5 class="fw-bold mb-3">Gửi câu hỏi cho shop</h5>

                    <form action="{{ route('profile.faq.send') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung câu hỏi</label>
                            <textarea name="message" rows="5" class="form-control">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-pink">
                            Gửi câu hỏi
                        </button>
                    </form>

                    @if(isset($myQuestions) && $myQuestions->count())
                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Câu hỏi của bạn</h5>

                        <div class="d-flex flex-column gap-3">
                            @foreach($myQuestions as $question)
                                <div class="question-box">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                        <div>
                                            <div class="fw-bold fs-5">{{ $question->subject }}</div>
                                            <div class="text-muted small">
                                                Gửi lúc: {{ $question->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </div>

                                        <div>
                                            @if($question->status === 'replied')
                                                <span class="badge bg-success">Đã trả lời</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Chờ trả lời</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="question-message mb-3">
                                        {{ $question->message }}
                                    </div>

                                    @if($question->reply)
                                        <div class="admin-reply">
                                            <div class="fw-bold mb-1">
                                                <i class="fas fa-reply me-1"></i>Phản hồi từ admin
                                            </div>
                                            <div>{{ $question->reply }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
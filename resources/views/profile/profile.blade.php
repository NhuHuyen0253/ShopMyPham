@extends('layout')

@section('content')
<div class="account-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>

            <li class="{{ request()->routeIs('profile.info') ? 'active' : '' }}">
            <a href="{{ route('profile.info') }}">Thông tin tài khoản</a>
            </li>

            <li class="{{ request()->routeIs('profile.orders') ? 'active' : '' }}">
            <a href="{{ route('profile.orders') }}">Đơn hàng của tôi</a>
            </li>

            <li class="{{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
            <a href="{{ route('wishlist.index') }}">Danh sách yêu thích</a>
            </li>

            <li class="{{ request()->routeIs('profile.rebuy') ? 'active' : '' }}">
            <a href="{{ route('profile.rebuy') }}">Mua lại</a>
            </li>

            <li class="{{ request()->routeIs('profile.faq') ? 'active' : '' }}">
            <a href="{{ route('profile.faq') }}">Hỏi đáp</a>
            </li>
        </ul>
    </div>

    <!-- Content (Thông tin tài khoản) -->
    <div class="account-info">
        <div class="profile-header">
            <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="Avatar" class="avatar">

            <div class="user-details">
                <h3>Chào <br> <strong>"{{ auth()->user()->name }}"</strong> </h3>
                <p>{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="edit-profile" style="font-size: large">Chỉnh sửa</a>
        </div>

        <div class="info-details">
            <h4>Thông tin tài khoản</h4>
            <div class="info-item">
                <span class="info-label">Số điện thoại:</span> {{ auth()->user()->phone }}
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span> {{ auth()->user()->email }}
            </div>
            <div class="info-item">
                <span class="info-label">Ngày tháng năm sinh: </span> {{ auth()->user()->dob }}
            </div>
          
            <div class="info-item">
                <span class="info-label">Giới tính:</span> {{ auth()->user()->gender ?? 'Chưa cập nhật' }}
            </div>
            <div class="info-item">
                <span class="info-label">Địa chỉ nhận hàng:</span> {{ auth()->user()->address ?? 'Chưa cập nhật' }}
            </div>
        </div>
    </div>
</div>
@endsection

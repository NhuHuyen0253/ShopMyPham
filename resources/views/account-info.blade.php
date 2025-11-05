@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/account-info.css') }}">

<div class="account-container">
    <div class="account-sidebar">
        <div class="user-greeting">
            <div class="user-avatar">
                <img src="{{ asset('images/user-avatar.png') }}" alt="Avatar" class="avatar-img">
            </div>
            <div>
                <strong>Chào {{ Auth::user()->name }}</strong><br>
                <a href="#">Chỉnh sửa tài khoản</a>
            </div>
        </div>
        <ul class="account-menu">
            <li class="active">Thông tin tài khoản</li>
            <li>Đơn hàng của tôi</li>
            <li>Booking của tôi</li>
            <li>Sổ địa chỉ nhận hàng</li>
            <li>Mua lại</li>
            <li>Hỏi đáp</li>
        </ul>
    </div>

    <div class="account-details">
        <h2>Thông tin tài khoản</h2>
        <form method="POST" action="{{ route('updateAccount') }}">
            @csrf
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" value="{{ Auth::user()->phone ?? 'Chưa cập nhật' }}" disabled>
            </div>
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="name" value="{{ Auth::user()->name }}">
            </div>
            <div class="form-group">
                <label>Giới tính</label>
                <div class="gender-options">
                    <label><input type="radio" name="gender" value="female" {{ Auth::user()->gender == 'female' ? 'checked' : '' }}> Nữ</label>
                    <label><input type="radio" name="gender" value="male" {{ Auth::user()->gender == 'male' ? 'checked' : '' }}> Nam</label>
                    <label><input type="radio" name="gender" value="other" {{ Auth::user()->gender == 'other' ? 'checked' : '' }}> Không xác định</label>
                </div>
            </div>
            <div class="form-group">
                <label>Ngày sinh</label>
                <div class="dob-selects" style="display: flex; gap: 10px;">
                    <select name="day" class="form-control">
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}" {{ old('day', isset($day) ? $day : '') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endfor
                    </select>

                    <select name="month" class="form-control">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', isset($month) ? $month : '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>

                    <select name="year" class="form-control">
                        @for ($y = now()->year; $y >= 1900; $y--)
                            <option value="{{ $y }}" {{ old('year', isset($year) ? $year : '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-update">Cập nhật</button>
        </form>
    </div>
</div>

@endsection

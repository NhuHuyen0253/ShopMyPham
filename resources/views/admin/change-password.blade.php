@extends('admin.layout') 
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Thay đổi mật khẩu</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.password.update') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700">Mật khẩu hiện tại</label>
            <input type="password" name="current_password" required class="w-full mt-1 p-2 border rounded">
            @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Mật khẩu mới</label>
            <input type="password" name="new_password" required class="w-full mt-1 p-2 border rounded">
            @error('new_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Xác nhận mật khẩu mới</label>
            <input type="password" name="new_password_confirmation" required class="w-full mt-1 p-2 border rounded">
        </div>

        <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">
            Cập nhật mật khẩu
        </button>
    </form>
</div>
@endsection

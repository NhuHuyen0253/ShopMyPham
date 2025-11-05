<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/detail.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        @include('admin.sidebar')
        <main class="flex-1 p-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

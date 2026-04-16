<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/home/xinhxinhshop.png') }}">
    <title>@yield('title', 'Admin')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    @vite(['resources/css/admin.css'])
    @yield('styles')
</head>
<body class="admin-body">
    <div class="d-flex admin-shell">
        @include('admin.sidebar')

        <div class="flex-fill admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-inner">
                    <div class="admin-topbar-right">
                        <div class="admin-chip">
                            <i class="fas fa-store text-pink-500"></i>
                            <span>XinhXinhShop Admin</span>
                        </div>

                        @auth('admin')
                            <a href="{{ route('admin.profile.show') }}" class="admin-chip admin-user-chip text-decoration-none">
                                <i class="fas fa-user-shield text-primary"></i>
                                <span>{{ auth('admin')->user()->name ?? auth('admin')->user()->phone ?? 'Admin' }}</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="admin-content">
                <div class="admin-content-inner">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
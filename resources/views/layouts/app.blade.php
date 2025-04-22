<!DOCTYPE html>
<html lang="en">
<head>
    @stack('head')
    <meta charset="UTF-8">
    <title>@yield('title', 'Kasir App')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CoreUI Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            z-index: 1020;
        }
        .sidebar-brand {
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-weight: 600;
            font-size: 18px;
            border-bottom: 1px solid #dee2e6;
        }
        .sidebar-nav .nav-link {
            padding: 10px 20px;
            color: #6c757d;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: 0.3s;
        }
        .sidebar-nav .nav-link i {
            margin-right: 10px;
            font-size: 16px;
        }
        .sidebar-nav .nav-link.active,
        .sidebar-nav .nav-link:hover {
            background-color: #0d6efd;
            color: white !important;
        }
        .sidebar-footer {
            padding: 12px 20px;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
        }

        .topbar {
            height: 56px;
            position: fixed;
            left: 220px;
            right: 0;
            top: 0;
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            z-index: 1030;
        }
        .content {
            margin-left: 220px;
            margin-top: 56px;
            padding: 24px;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .dropdown-menu {
            font-size: 14px;
        }
    </style>
</head>
<body>
@stack('scripts')

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" height="28" class="me-2">
            Kasir App
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/produk" class="nav-link {{ request()->is('produk*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/pembelian" class="nav-link {{ request()->is('pembelian*') ? 'active' : '' }}">
                        <i class="fas fa-cart-plus"></i> Pembelian
                    </a>
                </li>
                @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a href="/user" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> User
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="text-muted">Login sebagai:</div>
            <div class="fw-semibold">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <form action="{{ url()->current() }}" method="GET" class="input-group w-25">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari...">
        </form>


        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}" class="rounded-circle" width="35" alt="User">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li class="px-3 py-2">
                        <strong>{{ Auth::user()->name }}</strong><br>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?')">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

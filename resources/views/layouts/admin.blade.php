<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — D'CemilinYuk</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Fonts: Plus Jakarta Sans & Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Admin Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">

    {{-- Mobile Top Navigation --}}
    <div class="mobile-topbar">
        <div class="admin-logo" style="margin-bottom: 0;">
            <div class="logo-icon-box">
                <i class="fa-solid fa-snowflake"></i>
            </div>
            <div class="logo-text">D'Cemilin<span>Yuk</span></div>
        </div>
        <button class="btn-menu-toggle" id="btnToggleSidebar" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div class="admin-wrapper">
        {{-- Left Sidebar --}}
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-top">
                {{-- Brand Header --}}
                <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                    <div class="logo-icon-box">
                        <i class="fa-solid fa-snowflake"></i>
                    </div>
                    <div class="logo-text">D'Cemilin<span>Yuk</span></div>
                </a>

                {{-- Navigation Links --}}
                <nav class="sidebar-nav">
                    {{-- 1. Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-shapes"></i>
                        <span>Dashboard</span>
                    </a>

                    {{-- 2. Riwayat Pesanan --}}
                    <a href="{{ route('admin.orders.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Riwayat Pesanan</span>
                    </a>

                    {{-- 3. Manage Produk --}}
                    <a href="{{ route('admin.products.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box"></i>
                        <span>Manage Produk</span>
                    </a>

                    {{-- 4. Manage Kategori --}}
                    <a href="{{ route('admin.categories.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tag"></i>
                        <span>Manage Kategori</span>
                    </a>

                    {{-- 5. Manage Stock --}}
                    <a href="{{ route('admin.stock.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Manage Stock</span>
                    </a>
                </nav>
            </div>

            {{-- Bottom Admin User Profile --}}
            <div class="sidebar-footer">
                <div class="user-meta-label">Logged in as Admin</div>
                <div class="user-profile-row">
                    <div class="user-info">
                        <div class="user-name">{{ session('admin_name') ?? (Auth::user()->name ?? 'Hendra Wijaya') }}</div>
                        <div class="user-email">{{ session('admin_email') ?? (Auth::user()->email ?? 'hendra@cemilin.com') }}</div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout-icon" title="Keluar / Logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content Canvas --}}
        <main class="admin-main">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="alert-box alert-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Modals placeholder --}}
    @yield('modals')

    {{-- Core Admin JS --}}
    <script>
        // Toggle mobile sidebar
        const btnToggle = document.getElementById('btnToggleSidebar');
        const sidebar = document.getElementById('adminSidebar');
        if (btnToggle && sidebar) {
            btnToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Generic Modal Helper
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('active');
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.remove('active');
        }

        // Close modal on backdrop click
        document.querySelectorAll('.modal-backdrop').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

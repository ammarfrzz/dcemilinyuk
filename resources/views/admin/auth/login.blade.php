<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Admin — D'CemilinYuk</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-login-body">
    <div class="login-bg-glow-1"></div>
    <div class="login-bg-glow-2"></div>

    <div class="login-card">
        {{-- Brand Logo --}}
        <div class="login-brand">
            <div class="logo-icon-box">
                <i class="fa-solid fa-snowflake"></i>
            </div>
            <div class="logo-text">D'Cemilin<span>Yuk</span></div>
        </div>

        {{-- Header Info --}}
        <div class="login-header">
            <h1 class="login-title">Admin Portal</h1>
            <p class="login-subtitle">Masuk untuk mengelola operasional dan pesanan toko secara real-time.</p>
        </div>

        {{-- Alerts --}}
        @if ($errors->any())
            <div class="alert-box alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="alert-box alert-info">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        {{-- Login Form --}}
        <form action="{{ route('admin.login.submit') }}" method="POST" id="adminLoginForm">
            @csrf

            <div class="login-input-group">
                <label for="email" class="login-input-label">Alamat Email</label>
                <div class="login-input-wrapper">
                    <i class="fa-regular fa-envelope prefix-icon"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="login-input" 
                           placeholder="nama@cemilin.com" 
                           value="{{ old('email', 'hendra@cemilin.com') }}" 
                           required 
                           autofocus>
                </div>
            </div>

            <div class="login-input-group">
                <label for="password" class="login-input-label">Kata Sandi</label>
                <div class="login-input-wrapper">
                    <i class="fa-solid fa-lock prefix-icon"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="login-input" 
                           placeholder="••••••••" 
                           value="password"
                           required>
                    <button type="button" class="login-toggle-pwd" id="togglePasswordBtn" aria-label="Toggle password visibility">
                        <i class="fa-regular fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="login-meta-row">
                <label class="login-remember">
                    <input type="checkbox" name="remember" id="remember" checked style="accent-color: #ff6a00;">
                    <span>Ingat saya</span>
                </label>
                <a href="#" style="color: #ff8a3d; text-decoration: none; font-size: 13px;" onclick="alert('Untuk mereset password, silakan hubungi tim IT Administrator.'); return false;">Lupa Sandi?</a>
            </div>

            <button type="submit" class="login-btn-submit">
                Masuk ke Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 6px;"></i>
            </button>
        </form>

        {{-- Quick Demo Fill Box --}}
        <div class="login-demo-box" id="btnQuickFill" title="Klik untuk mengisi data demo otomatis">
            <div class="demo-box-label"><i class="fa-solid fa-bolt"></i> Akun Demo Tersedia</div>
            <div class="demo-box-creds">
                Email: <strong>hendra@cemilin.com</strong> &bull; Sandi: <strong>password</strong>
            </div>
            <div style="font-size: 11px; color: #ff8a3d; margin-top: 4px;">Klik untuk isi instan</div>
        </div>

        <a href="{{ route('home') }}" class="login-back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog Toko
        </a>
    </div>

    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const pwdInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        }

        // Quick demo fill
        const btnQuickFill = document.getElementById('btnQuickFill');
        if (btnQuickFill) {
            btnQuickFill.addEventListener('click', () => {
                document.getElementById('email').value = 'hendra@cemilin.com';
                document.getElementById('password').value = 'password';
            });
        }
    </script>
</body>
</html>

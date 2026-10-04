{{-- Layout utama DcemilinYuk — head, fonts, body wrapper --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DcemilinYuk - Katalog cemilan dan minuman dari pedagang kecil lokal.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('dcemilinyuk.brand') }} — Cemilan & Minuman Favorit</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Fonts: Playfair Display (display) + Inter (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#1A2942">

    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app">
        @include('partials.preloader')
        <div class="grain"></div>
        <div class="scroll-progress" id="scroll-progress"></div>
        @include('partials.navigation')

        <main class="main" id="main">
            @yield('content')
        </main>

        @include('partials.order-modal')
    </div>
</body>
</html>

@extends('layouts.app')

@section('content')
    {{-- Halaman Awal / Hero --}}
    @include('partials.hero')
    <div class="section-divider"></div>

    {{-- 1. Tentang kami --}}
    @include('partials.about')
    <div class="section-divider"></div>

    {{-- 2. Kenapa pilih kami --}}
    @include('partials.peel-reveal')
    <div class="section-divider"></div>

    {{-- 3. Cara pesan --}}
    @include('partials.how-to-order')
    <div class="section-divider"></div>

    {{-- 4. Pilih kategori favoritmu --}}
    @include('partials.categories')
    <div class="section-divider"></div>

    {{-- 5. Makanan terlaris --}}
    @include('partials.featured')
    <div class="section-divider"></div>

    {{-- 6. Semua produk kami --}}
    @include('partials.products')
    <div class="section-divider"></div>

    {{-- 7. Galeri kami --}}
    @include('partials.gallery')
    <div class="section-divider"></div>

    {{-- 8. Testimoni --}}
    @include('partials.testimonials')

    {{-- 9. Siap order sekarang --}}
    @include('partials.cta')

    {{-- 10. Footer --}}
    @include('partials.footer')
@endsection

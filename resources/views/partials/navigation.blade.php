{{-- Navbar horizontal: scroll blur, active underline, mobile menu (port Navigation.tsx) --}}
<nav class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="#hero" class="navbar-brand" data-scroll-to="hero">
            <span>{{ config('dcemilinyuk.brand') }}</span>
        </a>

        <div class="nav-links" id="nav-links">
            <a href="#hero" data-scroll-to="hero" data-section="hero" class="active">Beranda</a>
            <a href="#about" data-scroll-to="about" data-section="about">Tentang</a>
            <a href="#peel" data-scroll-to="peel" data-section="peel">Kenapa Kami</a>
            <a href="#howto" data-scroll-to="howto" data-section="howto">Cara Pesan</a>
            <a href="#categories" data-scroll-to="categories" data-section="categories">Kategori</a>
            <a href="#products" data-scroll-to="products" data-section="products">Produk</a>
            <a href="#gallery" data-scroll-to="gallery" data-section="gallery">Galeri</a>
            <a href="#contact" data-scroll-to="contact" data-section="contact">Kontak</a>
            <span class="nav-indicator" id="nav-indicator"></span>
        </div>

        <div class="nav-actions">
            <a href="#products" data-scroll-to="products" class="btn-primary btn-sm btn-nav-order">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Pesan Sekarang</span>
            </a>

            <div class="mobile-toggle" id="mobile-toggle" role="button" aria-label="Menu">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</nav>

<div class="mobile-menu" id="mobile-menu">
    <a href="#hero" data-scroll-to="hero" data-section="hero" class="active">Beranda</a>
    <a href="#about" data-scroll-to="about" data-section="about">Tentang</a>
    <a href="#peel" data-scroll-to="peel" data-section="peel">Kenapa Kami</a>
    <a href="#howto" data-scroll-to="howto" data-section="howto">Cara Pesan</a>
    <a href="#categories" data-scroll-to="categories" data-section="categories">Kategori</a>
    <a href="#products" data-scroll-to="products" data-section="products">Produk</a>
    <a href="#gallery" data-scroll-to="gallery" data-section="gallery">Galeri</a>
    <a href="#contact" data-scroll-to="contact" data-section="contact">Kontak</a>
    <a href="#products" data-scroll-to="products" style="color: var(--color-accent); font-weight: 700;">
        <i class="fa-solid fa-cart-shopping"></i> Pesan Sekarang
    </a>
</div>

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
            <a href="https://wa.me/{{ config('dcemilinyuk.wa_number') }}"
               target="_blank" rel="noopener noreferrer" class="btn-wa btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.558 4.116 1.535 5.845L.057 23.997l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.956 0-3.783-.574-5.318-1.562l-.38-.23-3.742.981.998-3.648-.248-.396A9.962 9.962 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
                <span class="btn-wa-text">Pesan WA</span>
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
    <a href="https://wa.me/{{ config('dcemilinyuk.wa_number') }}"
       target="_blank" rel="noopener noreferrer" style="color: #25D366; font-weight: 600;">
        Hubungi via WhatsApp
    </a>
</div>

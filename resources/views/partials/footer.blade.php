{{-- Footer links (port Footer.tsx) --}}
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">{{ config('dcemilinyuk.brand') }}</div>
                <p class="footer-desc text-body">Katalog cemilan dan minuman terbaik dari pedagang kecil lokal.</p>
            </div>
            <div>
                <h4>Menu</h4>
                <div class="footer-links">
                    <a href="#hero" data-scroll-to="hero">Beranda</a>
                    <a href="#products" data-scroll-to="products">Produk</a>
                    <a href="#about" data-scroll-to="about">Tentang</a>
                    <a href="#contact" data-scroll-to="contact">Kontak</a>
                </div>
            </div>
            <div>
                <h4>Bantuan</h4>
                <div class="footer-links">
                    <a href="#howto" data-scroll-to="howto">Cara Pesan</a>
                    <a href="#">FAQ</a>
                    <a href="#">Ketentuan Layanan</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ config('dcemilinyuk.brand') }} &mdash; Dibuat untuk pedagang lokal Indonesia.</p>
        </div>
    </div>
</footer>

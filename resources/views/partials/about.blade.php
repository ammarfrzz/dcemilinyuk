{{-- About: clip-path reveal + stat counter animation (port About.tsx) --}}
<section class="section" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-image" data-clip-reveal>
                <img src="https://images.unsplash.com/photo-1567521464027-f127ff144326?w=800&q=80"
                     alt="Pedagang lokal DcemilinYuk">
            </div>

            <div class="about-content">
                <div class="about-title" data-text-reveal>
                    <div class="section-subtitle text-sm">Tentang Kami</div>
                    <h2 class="heading-lg">Dibuat untuk <span class="text-accent">Pedagang Lokal</span></h2>
                </div>

                <p class="text-body about-desc">
                    DcemilinYuk adalah platform katalog yang membantu pedagang kecil lokal Indonesia
                    menjangkau lebih banyak pelanggan. Kami menyediakan berbagai cemilan dan minuman
                    berkualitas dengan harga terjangkau.
                </p>

                <p class="text-body about-desc">
                    Dengan memesan melalui WhatsApp, prosesnya mudah dan cepat. Tidak perlu ribet,
                    langsung chat dan pesan!
                </p>

                <div class="about-stats" data-stats-counter>
                    <div class="about-stat">
                        <div class="about-stat__number" data-target="{{ count($categories) }}">0</div>
                        <div class="about-stat__label text-sm">Kategori</div>
                    </div>
                    <div class="about-stat">
                        <div class="about-stat__number" data-target="{{ $products->count() }}">0</div>
                        <div class="about-stat__label text-sm">Produk</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

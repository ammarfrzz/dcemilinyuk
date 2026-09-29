{{-- Hero: word-by-word text reveal + parallax background (port Hero.tsx) --}}
<section class="hero" id="hero">
    <div class="hero-bg">
        <div class="hero-bg-image" data-parallax-bg
             style="background-image: url('https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=1920&q=80')"></div>
        <div class="hero-bg-overlay"></div>
    </div>

    <div class="hero__content container" id="hero-content">
        <div class="hero-eyebrow">
            <span class="hero-eyebrow__line"></span>
            <span>Cemilan & Minuman Terbaik</span>
        </div>

        <h1 class="heading-xl hero-title" id="hero-title">
            <span class="hero-line">
                <span class="hero-word">Jajanan</span>{{ ' ' }}
                <span class="hero-word hero-word--accent">Favorit</span>
            </span>
            <span class="hero-line">
                <span class="hero-word">Kamu</span>{{ ' ' }}
                <span class="hero-word">Ada</span>{{ ' ' }}
                <span class="hero-word hero-word--italic">di Sini</span>
            </span>
        </h1>

        <div class="hero-subtitle" id="hero-subtitle">
            <p class="hero-sub-item">
                Temukan berbagai cemilan dan minuman lezat dari pedagang kecil terpercaya.
            </p>
            <p class="hero-sub-item">
                Pesan langsung via WhatsApp, mudah dan cepat!
            </p>
        </div>

        <div class="hero-cta-group">
            <button class="btn-primary" data-scroll-to="products">
                <span>Lihat Produk</span>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5 10H15M15 10L10 5M15 10L10 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="scroll-indicator">
        <div class="scroll-line"></div>
        <span class="text-sm">Scroll</span>
    </div>
</section>

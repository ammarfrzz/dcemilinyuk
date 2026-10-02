{{-- Hero: Takapedia-style Promotional Banner Carousel --}}
<section class="hero-banner-section" id="hero">
    <div class="container hero-banner-container">
        {{-- Banner Carousel Wrapper --}}
        <div class="banner-carousel" id="banner-carousel" data-banner-carousel>
            <div class="banner-carousel__track" id="banner-carousel-track">

                {{-- Slide 1: Risol Mayo & Aneka Gorengan --}}
                <div class="banner-slide banner-slide--active" data-slide-index="0"
                     style="--slide-bg: url('https://images.unsplash.com/photo-1541529086526-db283c563270?w=1600&auto=format&fit=crop&q=80')">
                    <div class="banner-slide__bg"></div>
                    <div class="banner-slide__overlay"></div>
                    <div class="banner-slide__content">
                        <div class="banner-chip banner-chip--hot">
                            <i class="fa-solid fa-fire"></i>
                            <span>Best Seller Pekan Ini</span>
                        </div>
                        <h1 class="banner-title">
                            Sensasi Renyah Gurih <br>
                            <span class="text-accent">Risol Mayo & Gorengan</span>
                        </h1>
                        <p class="banner-desc">
                            Kulit renyah keemasan dengan isian smoked beef gurih dan mayones melimpah. Fresh digoreng setiap hari langsung dari pedagang lokal terpercaya!
                        </p>
                    </div>
                </div>

                {{-- Slide 2: Minuman Segar --}}
                <div class="banner-slide" data-slide-index="1"
                     style="--slide-bg: url('https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=1600&auto=format&fit=crop&q=80')">
                    <div class="banner-slide__bg"></div>
                    <div class="banner-slide__overlay"></div>
                    <div class="banner-slide__content">
                        <div class="banner-chip banner-chip--fresh">
                            <i class="fa-solid fa-snowflake"></i>
                            <span>Kesegaran Maksimal</span>
                        </div>
                        <h2 class="banner-title">
                            Es Teh Tarik & <br>
                            <span class="text-accent">Kopi Susu Creamy</span>
                        </h2>
                        <p class="banner-desc">
                            Sensasi dingin teh tarik racikan otentik dan kopi susu gula aren yang creamy. Melepas dahaga dan bikin harimu kembali ceria!
                        </p>
                    </div>
                </div>

                {{-- Slide 3: Nasi Uduk & Chicken Katsu --}}
                <div class="banner-slide" data-slide-index="2"
                     style="--slide-bg: url('https://images.unsplash.com/photo-1512058564366-18510be2db19?w=1600&auto=format&fit=crop&q=80')">
                    <div class="banner-slide__bg"></div>
                    <div class="banner-slide__overlay"></div>
                    <div class="banner-slide__content">
                        <div class="banner-chip banner-chip--warm">
                            <i class="fa-solid fa-utensils"></i>
                            <span>Menu Kenyang Mantap</span>
                        </div>
                        <h2 class="banner-title">
                            Nasi Uduk Gurih & <br>
                            <span class="text-accent">Chicken Katsu Crispy</span>
                        </h2>
                        <p class="banner-desc">
                            Menu hangat yang mengenyangkan untuk sarapan, makan siang, maupun malam. Porsi mantap harga ramah di kantong!
                        </p>
                    </div>
                </div>

                {{-- Slide 4: Bolen Pisang & Donat Kentang --}}
                <div class="banner-slide" data-slide-index="3"
                     style="--slide-bg: url('https://images.unsplash.com/photo-1551024601-bec78aea704b?w=1600&auto=format&fit=crop&q=80')">
                    <div class="banner-slide__bg"></div>
                    <div class="banner-slide__overlay"></div>
                    <div class="banner-slide__content">
                        <div class="banner-chip banner-chip--sweet">
                            <i class="fa-solid fa-cake-candles"></i>
                            <span>Manis & Lumer di Lidah</span>
                        </div>
                        <h2 class="banner-title">
                            Bolen Pisang Renyah & <br>
                            <span class="text-accent">Donat Kentang Lembut</span>
                        </h2>
                        <p class="banner-desc">
                            Pastry homemade renyah berpadu pisang manis, coklat, dan keju melimpah. Camilan manis istimewa favorit seluruh keluarga!
                        </p>
                    </div>
                </div>

            </div>

            {{-- Navigation Buttons (Takapedia Floating Style) --}}
            <button type="button" class="banner-nav-btn banner-nav-btn--prev" id="banner-prev" aria-label="Slide sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="banner-nav-btn banner-nav-btn--next" id="banner-next" aria-label="Slide berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            {{-- Indicators / Pagination Pills (Takapedia Style) --}}
            <div class="banner-pagination" id="banner-pagination">
                <button type="button" class="banner-dot banner-dot--active" data-slide="0" aria-label="Slide 1"></button>
                <button type="button" class="banner-dot" data-slide="1" aria-label="Slide 2"></button>
                <button type="button" class="banner-dot" data-slide="2" aria-label="Slide 3"></button>
                <button type="button" class="banner-dot" data-slide="3" aria-label="Slide 4"></button>
            </div>
        </div>
    </div>
</section>

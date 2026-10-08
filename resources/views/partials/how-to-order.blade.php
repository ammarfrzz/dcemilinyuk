{{-- HowToOrder: step cards dengan foto visual eksternal & staggered reveal --}}
<section class="section howto-section" id="howto">
    <div class="container">
        <div class="section-header">
            <div data-text-reveal>
                <div class="section-label">Cara Pesan</div>
                <h2 class="heading-lg">
                    <span class="text-line">Mudah & Cepat</span>
                    <span class="text-line">Pesan & <em>Bayar</em></span>
                </h2>
            </div>
            <p class="text-body">Pesan cemilan favoritmu langsung lewat website dengan 3 langkah mudah dan praktis.</p>
        </div>

        <div class="steps-grid" data-stagger-reveal data-reveal-target=".step-card">
            <div class="step-card">
                <div class="step-card__media">
                    <img src="{{ asset('images/howto/step-1.webp') }}" alt="Pilih Menu" loading="lazy" class="step-card__img">
                    <div class="step-card__num">01</div>
                </div>
                <div class="step-card__body">
                    <div class="step-card__icon"><i class="fa-solid fa-utensils"></i></div>
                    <h4 class="step-card__title">Pilih Menu Favorit</h4>
                    <p class="step-card__desc text-body">
                        Lihat katalog cemilan dan minuman lezat kami, lalu klik tombol <strong>Pesan Sekarang</strong> pada menu pilihanmu.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-card__media">
                    <img src="{{ asset('images/howto/step-2.webp') }}" alt="Isi Data & Alamat" loading="lazy" class="step-card__img">
                    <div class="step-card__num">02</div>
                </div>
                <div class="step-card__body">
                    <div class="step-card__icon"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h4 class="step-card__title">Isi Data & Alamat</h4>
                    <p class="step-card__desc text-body">
                        Isi form data diri dan alamat pengantaran lengkap agar kurir kami dapat mengantarkan pesanan dengan tepat waktu.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-card__media">
                    <img src=" {{ asset('images/howto/step-3.webp') }}" alt="Pilih Pembayaran" loading="lazy" class="step-card__img">
                    <div class="step-card__num">03</div>
                </div>
                <div class="step-card__body">
                    <div class="step-card__icon"><i class="fa-solid fa-credit-card"></i></div>
                    <h4 class="step-card__title">Pilih Pembayaran & Nikmati</h4>
                    <p class="step-card__desc text-body">
                        Review pesanan dan pilih metode pembayaran favoritmu: <strong>Transfer Bank, QRIS, atau COD</strong> saat pesanan tiba!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

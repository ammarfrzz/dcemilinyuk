{{-- HowToOrder: step cards dengan staggered reveal (port HowToOrder.tsx) --}}
<section class="section" id="howto">
    <div class="container">
        <div class="section-header">
            <div data-text-reveal>
                <div class="section-subtitle text-sm">Cara Pesan</div>
                <h2 class="heading-lg">
                    <span class="text-line">Mudah & Cepat</span>
                    <span class="text-line">via <em>WhatsApp</em></span>
                </h2>
            </div>
        </div>

        <div class="steps-grid" data-stagger-reveal data-reveal-target=".step-card">
            <div class="step-card">
                <div class="step-card__num">01</div>
                <div class="step-card__icon"><i class="fa-solid fa-eye"></i></div>
                <h4 class="step-card__title">Pilih Produk</h4>
                <p class="step-card__desc text-body">
                    Lihat katalog produk kami dan temukan cemilan atau minuman yang kamu inginkan.
                </p>
            </div>
            <div class="step-card">
                <div class="step-card__num">02</div>
                <div class="step-card__icon"><i class="fa-solid fa-comment-dots"></i></div>
                <h4 class="step-card__title">Klik Pesan WA</h4>
                <p class="step-card__desc text-body">
                    Tekan tombol "Pesan" di produk pilihan. Pesan otomatis akan terbuat di WhatsApp.
                </p>
            </div>
            <div class="step-card">
                <div class="step-card__num">03</div>
                <div class="step-card__icon"><i class="fa-solid fa-check"></i></div>
                <h4 class="step-card__title">Konfirmasi & Bayar</h4>
                <p class="step-card__desc text-body">
                    Diskusikan detail pengiriman dan pembayaran langsung dengan kami via WhatsApp.
                </p>
            </div>
        </div>
    </div>
</section>

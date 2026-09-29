{{-- Testimonials: auto-rotating quotes (port Testimonials.tsx) --}}
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header">
            <div data-text-reveal>
                <div class="section-subtitle text-sm">Testimoni</div>
                <h2 class="heading-lg">
                    <span class="text-line">Apa Kata</span>
                    <span class="text-line"><em>Mereka?</em></span>
                </h2>
            </div>
        </div>

        <div class="testimonials-content" data-testimonials>
            @php
                $testimonials = [
                    ['text' => 'Es Teh Tariknya enak banget! Segar dan manisnya pas. Sudah langganan tiap minggu!', 'author' => 'Aisyah R.', 'role' => 'Pelanggan Setia', 'stars' => 5],
                    ['text' => 'Bolen Pisangnya renyah dan isian pisangnya banyak. Anak-anak suka semua! Pasti repeat order!', 'author' => 'Rizky M.', 'role' => 'Pelanggan Baru', 'stars' => 5],
                    ['text' => 'Pengirimannya cepat dan packaging-nya rapi banget. Pesanan sampai dengan selamat!', 'author' => 'Dewi S.', 'role' => 'Pelanggan Setia', 'stars' => 5],
                ];
            @endphp

            <div class="testimonial-card" data-testimonial-card>
                <div class="testimonial-stars" data-testimonial-stars></div>
                <div class="testimonial-text" data-testimonial-text></div>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" data-testimonial-avatar></div>
                    <div class="testimonial-info">
                        <div class="testimonial-name" data-testimonial-name></div>
                        <div class="testimonial-role text-sm" data-testimonial-role></div>
                    </div>
                </div>
            </div>

            {{-- Data testimoni disuntikkan sebagai JSON untuk rotasi di JS --}}
            <script type="application/json" data-testimonials-data>{{ json_encode($testimonials) }}</script>

            <div class="testimonial-dots" data-testimonial-dots>
                @foreach ($testimonials as $i => $t)
                    <button class="testimonial-dot {{ $i === 0 ? 'testimonial-dot--active' : '' }}"
                            data-testimonial-index="{{ $i }}"
                            aria-label="Testimonial {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

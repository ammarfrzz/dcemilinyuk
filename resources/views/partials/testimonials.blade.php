{{-- Testimonials: Auto-rotating customer reviews with Indonesian casual gimmick --}}
@php
    $testimonials = [
        [
            'text' => 'Asli bolen pisangnya bikin gagal diet! Rencana mau nyemil satu doang pas push rank ML, eh tau-tau satu box ludes sendiri wkwk. Mantap parah renyahnya!',
            'author' => 'Dimas "Gepeng"',
            'role' => 'Spesialis Begadang & Push Rank',
            'stars' => 5,
            'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80',
            'tag' => 'Langganan Cemilan',
        ],
        [
            'text' => 'Es Teh Tariknya gila sih seger beneran, bukan yang manis nyelekit di tenggorokan gitu. Pas banget diminum pas lagi pusing mikirin revisi skripsi 😭🔥',
            'author' => 'Nadhira Putri',
            'role' => 'Si Paling Butuh yang Manis',
            'stars' => 5,
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
            'tag' => 'Pecinta Minuman Segar',
        ],
        [
            'text' => 'Harga bersahabat banget di kantong akhir bulan, tapi rasa bintang lima bro! Risol mayonya lumer pas digigit, temen-temen kosan sampe pada ikutan nitip.',
            'author' => 'Mas Bagas',
            'role' => 'Duta Anak Kosan',
            'stars' => 5,
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            'tag' => 'Spesialis Risol Mayo',
        ],
        [
            'text' => 'Awalnya cuma iseng beli basreng pedas daun jeruknya, eh ketagihan dong... gurih renyah dan bumbunya nampol, ga alot sama sekali. Auto langganan tetap!',
            'author' => 'Kak Bella',
            'role' => 'Penyuka Cemilan Pedas',
            'stars' => 5,
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            'tag' => 'Pecinta Pedas Nampol',
        ],
        [
            'text' => 'Packaging-nya rapi banget, nyampe kantor masih fresh dan higienis. Sekali buka di pantry langsung ludes diserbu anak satu divisi. Besok wajib order lagi sih!',
            'author' => 'Fajar Kurniawan',
            'role' => 'Tim Ngemil Kantor',
            'stars' => 5,
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            'tag' => 'Order Snack Box',
        ],
    ];
    $active = $testimonials[0];
@endphp

<section class="section testimonials-section" id="testimonials">
    <div class="testimonials-ambient-glow testimonials-ambient-glow--1"></div>
    <div class="testimonials-ambient-glow testimonials-ambient-glow--2"></div>

    <div class="container">
        <div class="section-header text-center">
            <div data-text-reveal>
                <div class="section-label">
                    <i class="fa-solid fa-heart me-1"></i> Ulasan Cemilers
                </div>
                <h2 class="heading-lg">
                    <span class="text-line">Apa Kata</span>
                    <span class="text-line"><em>Mereka?</em></span>
                </h2>
                <p class="text-body" style="max-width: 520px; margin: 0 auto;">
                    Cerita jujur dan pengalaman seru dari temen-temen yang udah nyobain enaknya cemilan DcemilinYuk!
                </p>
            </div>
        </div>

        <div class="testimonials-content" data-testimonials>
            {{-- Kartu Testimoni dengan Desain Modern Glassmorphism --}}
            <div class="testimonial-card" data-testimonial-card>
                {{-- Quote Watermark Background Icon --}}
                <div class="testimonial-quote-bg" aria-hidden="true">
                    <i class="fa-solid fa-quote-right"></i>
                </div>

                {{-- Card Top Header: Stars & Score --}}
                <div class="testimonial-card__header">
                    <div class="testimonial-stars-wrap">
                        <div class="testimonial-stars" data-testimonial-stars>
                            @for ($s = 0; $s < $active['stars']; $s++)
                                <i class="fa-solid fa-star"></i>
                            @endfor
                        </div>
                        <span class="testimonial-rating-score">5.0</span>
                    </div>
                </div>

                {{-- Quote Body --}}
                <div class="testimonial-body">
                    <div class="testimonial-text" data-testimonial-text>
                        "{{ $active['text'] }}"
                    </div>
                </div>

                {{-- Card Footer: Author Profile --}}
                <div class="testimonial-author">
                    <div class="testimonial-avatar" data-testimonial-avatar>
                        <img src="{{ $active['avatar'] }}" alt="{{ $active['author'] }}" class="testimonial-avatar-img" loading="lazy">
                    </div>
                    <div class="testimonial-info">
                        <div class="testimonial-name" data-testimonial-name>{{ $active['author'] }}</div>
                        <div class="testimonial-role" data-testimonial-role>{{ $active['role'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Controls: Navigasi Prev/Next & Dots yang Elegan --}}
            <div class="testimonial-controls">
                <button type="button" class="testimonial-nav-btn prev-btn" id="testimonial-prev" aria-label="Testimoni Sebelumnya">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <div class="testimonial-dots" data-testimonial-dots>
                    @foreach ($testimonials as $i => $t)
                        <button class="testimonial-dot {{ $i === 0 ? 'testimonial-dot--active' : '' }}"
                                data-testimonial-index="{{ $i }}"
                                type="button"
                                aria-label="Lihat testimoni {{ $i + 1 }} dari {{ $t['author'] }}"></button>
                    @endforeach
                </div>

                <button type="button" class="testimonial-nav-btn next-btn" id="testimonial-next" aria-label="Testimoni Selanjutnya">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            {{-- JSON Data untuk rotasi otomatis --}}
            <script type="application/json" id="testimonials-json-data" data-testimonials-data>{!! json_encode($testimonials) !!}</script>
        </div>
    </div>
</section>

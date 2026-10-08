{{-- HorizontalScroll: galeri horizontal dengan GSAP pin + scrub (port HorizontalScroll.tsx) --}}
<section class="hscroll" id="horizontal">
    <div class="container">
        <div class="hscroll__title heading-lg" data-text-reveal>
            <span class="text-line">Suasana</span>
            <span class="text-line"><em>DcemilinYuk</em></span>
        </div>
        <p class="text-body hscroll__subtitle">
            Scroll ke bawah untuk menjelajahi — konten bergerak horizontal.
        </p>
    </div>

    @php
        $hscrollImages = [
            ['src' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&fm=webp&q=80', 'alt' => 'Dapur Cemilan Segar'],
            ['src' => asset('images/products/risol-mayo.webp'), 'alt' => 'Risol & Gorengan Renyah'],
            ['src' => asset('images/products/kopi-susu.webp'), 'alt' => 'Kopi Susu Racikan'],
            ['src' => asset('images/products/es-teh-tarik.webp'), 'alt' => 'Es Teh Tarik Dingin'],
            ['src' => asset('images/products/bolen-pisang.webp'), 'alt' => 'Bolen & Pastry Homemade'],
            ['src' => asset('images/products/nasi-uduk.webp'), 'alt' => 'Nasi Uduk & Chicken Katsu'],
        ];
    @endphp

    <div class="hscroll__container" data-hscroll-container>
        <div class="hscroll__strip" data-hscroll-strip>
            @foreach ($hscrollImages as $img)
                <div class="hscroll__item">
                    <div class="hscroll__item-image">
                        <img src="{{ $img['src'] }}" alt="{{ $img['alt'] }}" loading="lazy">
                    </div>
                    <span class="hscroll__item-label text-sm">{{ $img['alt'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

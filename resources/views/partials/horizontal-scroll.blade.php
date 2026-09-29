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
            ['src' => 'https://images.unsplash.com/photo-1567521464027-f127ff144326?w=800&q=80', 'alt' => 'Suasana kedai'],
            ['src' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80', 'alt' => 'Interior hangat'],
            ['src' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80', 'alt' => 'Area duduk'],
            ['src' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80', 'alt' => 'Latte art'],
            ['src' => 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=800&q=80', 'alt' => 'Pour over'],
            ['src' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&q=80', 'alt' => 'Secangkir kopi'],
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

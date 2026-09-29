{{-- Gallery: clip-path image reveals (port Gallery.tsx) --}}
<section class="gallery section" id="gallery">
    <div class="container">
        <div class="gallery__header">
            <div class="gallery__title heading-lg" data-text-reveal>
                <span class="text-line">Galeri</span>
                <span class="text-line"><em>Kami</em></span>
            </div>
            <p class="text-body gallery__subtitle">
                Lihat langsung kelezatan cemilan dan minuman kami.
            </p>
        </div>

        @php
            $galleryImages = [
                ['src' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80', 'alt' => 'Cemilan lezat', 'span' => 'wide'],
                ['src' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80', 'alt' => 'Kopi susu', 'span' => 'tall'],
                ['src' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&q=80', 'alt' => 'Donat & pastry', 'span' => 'normal'],
                ['src' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=600&q=80', 'alt' => 'Nasi uduk', 'span' => 'normal'],
                ['src' => 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=600&q=80', 'alt' => 'Roti & bakery', 'span' => 'wide'],
                ['src' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=80', 'alt' => 'Makanan segar', 'span' => 'normal'],
            ];
        @endphp

        <div class="gallery__grid" data-gallery-grid>
            @foreach ($galleryImages as $img)
                <div class="gallery-item gallery-item--{{ $img['span'] }}">
                    <img src="{{ $img['src'] }}" alt="{{ $img['alt'] }}" loading="lazy">
                    <div class="gallery-item__overlay">
                        <span class="text-sm">{{ $img['alt'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

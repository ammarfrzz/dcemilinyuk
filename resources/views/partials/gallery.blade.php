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
                ['src' => asset('images/products/risol-mayo.webp'), 'alt' => 'Risol & Gorengan Renyah', 'span' => 'wide'],
                ['src' => asset('images/products/kopi-susu.webp'), 'alt' => 'Kopi Susu Gula Aren', 'span' => 'tall'],
                ['src' => asset('images/products/donat-kentang.webp'), 'alt' => 'Donat Kentang Manis', 'span' => 'normal'],
                ['src' => asset('images/products/chicken-katsu.webp'), 'alt' => 'Chicken Katsu Crispy', 'span' => 'normal'],
                ['src' => asset('images/products/bolen-pisang.webp'), 'alt' => 'Bolen Pisang Renyah', 'span' => 'wide'],
                ['src' => asset('images/products/es-teh-tarik.webp'), 'alt' => 'Es Teh Tarik Segar', 'span' => 'normal'],
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

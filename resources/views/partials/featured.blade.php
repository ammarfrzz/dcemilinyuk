{{-- Featured: best seller cards dari DB (port Featured.tsx) --}}
<section class="featured section" id="featured">
    <div class="container">
        <div class="section-header">
            <div data-text-reveal>
                <div class="section-label">Best Seller</div>
                <h2 class="heading-lg">
                    <span class="text-line">Makanan</span>
                    <span class="text-line"><em>Terlaris</em></span>
                </h2>
            </div>
            <p class="text-body">Produk favorit yang paling banyak dipesan pelanggan kami.</p>
        </div>

        <div class="featured-layout" data-featured-reveal>
            @if ($bestSellers->isNotEmpty())
                {{-- Hero card — paling besar di kiri --}}
                @php $heroProduct = $bestSellers->first() @endphp
                <div class="featured-hero">
                    <div class="featured-hero__image">
                        <img src="{{ $heroProduct->image_url }}" alt="{{ $heroProduct->name }}" loading="lazy">
                        <div class="featured-hero__gradient"></div>
                        <div class="featured-hero__badge">No. 1</div>
                    </div>
                    <div class="featured-hero__content">
                        <h3 class="featured-hero__name">{{ $heroProduct->name }}</h3>
                        <p class="featured-hero__desc">{{ $heroProduct->description }}</p>
                        <div class="featured-hero__bottom">
                            <span class="featured-hero__price">{{ $heroProduct->formatted_price }}</span>
                            <button type="button"
                                    class="btn-primary btn-primary--sm js-open-order-modal"
                                    data-id="{{ $heroProduct->id }}"
                                    data-name="{{ $heroProduct->name }}"
                                    data-price="{{ $heroProduct->price }}"
                                    data-formatted-price="{{ $heroProduct->formatted_price }}"
                                    data-image="{{ $heroProduct->image_url }}"
                                    data-category="Best Seller">
                                <span>Pesan Sekarang</span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 8H13M13 8L8 3M13 8L8 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Stacked items — kanan, posisi 02 dst --}}
            <div class="featured-stack">
                @foreach ($bestSellers->skip(1) as $item)
                    <div class="featured-item">
                        <div class="featured-item__num">{{ str_pad($loop->iteration + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="featured-item__image">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy">
                        </div>
                        <div class="featured-item__info">
                            <h4 class="featured-item__name">{{ $item->name }}</h4>
                            <span class="featured-item__price">{{ $item->formatted_price }}</span>
                        </div>
                        <button type="button"
                                class="featured-item__link js-open-order-modal"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}"
                                data-formatted-price="{{ $item->formatted_price }}"
                                data-image="{{ $item->image_url }}"
                                data-category="Best Seller">
                            Pesan
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                <path d="M3 8H13M13 8L8 3M13 8L8 13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

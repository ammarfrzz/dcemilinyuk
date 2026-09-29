{{-- Products: grid produk dari DB + filter tab kategori + tombol WA (port Products.tsx) --}}
<section class="section" id="products" style="background: var(--color-bg-warm)">
    <div class="container">
        <div class="section-header">
            <div class="section-subtitle text-sm">Produk</div>
            <h2 class="heading-lg">Semua <em>Produk</em> Kami</h2>
            <p class="text-body">Pilih cemilan dan minuman favoritmu, lalu pesan via WhatsApp!</p>
        </div>

        {{-- Filter tab kategori (Semua + tiap kategori dari DB metadata) --}}
        <div class="product-tabs" id="product-tabs">
            <button class="product-tab product-tab--active" data-filter="all">Semua</button>
            @foreach ($categories as $key => $category)
                <button class="product-tab" data-filter="{{ $key }}">{{ $category['name'] }}</button>
            @endforeach
        </div>

        <div class="products-grid" id="products-grid">
            @foreach ($products as $product)
                <div class="product-card" data-category="{{ $product->category }}">
                    @if ($product->badge)
                        <span class="product-badge {{ $product->badge_class }}">{{ $product->badge_label }}</span>
                    @endif
                    <div class="product-card__img-wrap">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="product-card__img" loading="lazy">
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__category">{{ $categories[$product->category]['name'] ?? $product->category }}</div>
                        <h4 class="product-card__name">{{ $product->name }}</h4>
                        <p class="product-card__desc text-body">{{ $product->description }}</p>
                        <div class="product-card__footer">
                            <div class="product-card__price">{{ $product->formatted_price }}</div>
                            <div class="product-card__meta">
                                <span class="product-card__rating">★ {{ $product->rating }}</span>
                                <span class="product-card__sold">{{ $product->sold }} terjual</span>
                            </div>
                        </div>
                        <a href="{{ $product->whatsapp_order_url }}"
                           target="_blank" rel="noopener noreferrer"
                           class="btn-primary product-card__btn">
                            <span>Pesan via WA</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.558 4.116 1.535 5.845L.057 23.997l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.956 0-3.783-.574-5.318-1.562l-.38-.23-3.742.981.998-3.648-.248-.396A9.962 9.962 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

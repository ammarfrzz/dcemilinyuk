{{-- Products: grid produk dari DB + filter tab kategori + tombol WA (port Products.tsx) --}}
<section class="section products-section" id="products">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Katalog Produk</div>
            <h2 class="heading-lg">Semua <em>Produk</em> Kami</h2>
            <p class="text-body">Pilih cemilan dan minuman favoritmu, lalu pesan dan bayar langsung di website!</p>
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
                        <button type="button"
                                class="btn-primary product-card__btn js-open-order-modal"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-formatted-price="{{ $product->formatted_price }}"
                                data-image="{{ $product->image_url }}"
                                data-category="{{ $categories[$product->category]['name'] ?? $product->category }}">
                            <span>Pesan Sekarang</span>
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

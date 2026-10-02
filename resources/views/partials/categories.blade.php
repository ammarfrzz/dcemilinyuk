{{-- Categories: scroll reveal cards dengan foto eksternal berkualitas tinggi --}}
<section class="section categories-section" id="categories">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Pilihan Kategori</div>
            <h2 class="heading-lg">Pilih Kategori <em>Favoritmu</em></h2>
            <p class="text-body">Berbagai pilihan cemilan dan minuman dari pedagang kecil terbaik.</p>
        </div>

        <div class="categories-grid" data-stagger-reveal data-reveal-target=".cat-card">
            @foreach ($categories as $key => $category)
                <div class="cat-card" data-category-filter="{{ $key }}" role="button" tabindex="0">
                    <div class="cat-card__thumb">
                        <img src="{{ $category['image'] ?? 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=600&auto=format&fit=crop&q=80' }}"
                             alt="{{ $category['name'] }}" loading="lazy" class="cat-card__img">
                        <div class="cat-card__overlay"></div>
                        <div class="cat-card__icon-badge">
                            <i class="{{ $category['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="cat-card__body">
                        <h4 class="cat-card__name">{{ $category['name'] }}</h4>
                        <p class="cat-card__desc text-body">{{ $category['description'] }}</p>
                        <div class="cat-card__action">
                            <span>Lihat Produk</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

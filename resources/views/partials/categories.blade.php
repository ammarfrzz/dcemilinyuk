{{-- Categories: scroll reveal cards (port Categories.tsx) --}}
<section class="section" id="categories">
    <div class="container">
        <div class="section-header">
            <div class="section-subtitle text-sm">Kategori</div>
            <h2 class="heading-lg">Pilih Kategori <em>Favoritmu</em></h2>
            <p class="text-body">Berbagai pilihan cemilan dan minuman dari pedagang kecil terbaik.</p>
        </div>

        <div class="categories-grid" data-stagger-reveal data-reveal-target=".cat-card">
            @foreach ($categories as $category)
                <div class="cat-card">
                    <div class="cat-card__icon">
                        <i class="{{ $category['icon'] }}"></i>
                    </div>
                    <h4 class="cat-card__name">{{ $category['name'] }}</h4>
                    <p class="cat-card__desc text-body">{{ $category['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

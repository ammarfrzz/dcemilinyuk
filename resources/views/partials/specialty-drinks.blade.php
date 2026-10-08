{{-- SpecialtyDrinks: menu signature dengan text scatter on hover (port SpecialtyDrinks.tsx) --}}
@php
    $signatureItems = $minumanProducts ?? collect([
        (object) [
            'name' => 'Es Teh Tarik',
            'description' => 'Teh tarik premium dengan rasa creamy, buih melimpah, dan manis yang pas',
            'price' => 8000,
            'image_url' => asset('images/products/es-teh-tarik.webp'),
        ],
        (object) [
            'name' => 'Kopi Susu',
            'description' => 'Kopi susu gula aren kekinian dengan perpaduan espresso mantap dan susu segar creamy',
            'price' => 12000,
            'image_url' => asset('images/products/kopi-susu.webp'),
        ],
        (object) [
            'name' => 'Es Buah',
            'description' => 'Es buah segar aneka buah tropis manis dengan sirup dan susu creamy menyegarkan',
            'price' => 10000,
            'image_url' => asset('images/products/es-buah.webp'),
        ],
    ]);
@endphp

<section class="specialty section" id="specialty">
    <div class="container">
        <div class="specialty__title heading-lg" data-text-reveal>
            <span class="text-line">Menu</span>
            <span class="text-line"><em>Signature</em></span>
        </div>
        <p class="text-body specialty__subtitle">
            Hover untuk lihat yang membuat setiap menu spesial.
        </p>

        <div class="specialty__list" data-specialty-list>
            @foreach ($signatureItems as $item)
                <div class="specialty-item">
                    <div class="specialty-item__bg" style="background-image: url('{{ $item->image_url }}')"></div>
                    <div class="specialty-item__content">
                        <span class="specialty-item__price">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <h3 class="specialty-item__name">{{ $item->name }}</h3>
                        <p class="specialty-item__desc text-body">{{ $item->description }}</p>
                    </div>
                    <div class="specialty-item__line"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>

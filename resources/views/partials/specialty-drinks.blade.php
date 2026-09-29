{{-- SpecialtyDrinks: menu signature dengan text scatter on hover (port SpecialtyDrinks.tsx) --}}
@php
    $signatureItems = $minumanProducts ?? collect([
        (object) [
            'name' => 'Es Teh Tarik',
            'description' => 'Teh tarik premium dengan rasa creamy dan manis yang pas',
            'price' => 8000,
            'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&q=80',
        ],
        (object) [
            'name' => 'Bolen Pisang',
            'description' => 'Bolen pisang homemade dengan kulit renyah dan isian pisang melimpah',
            'price' => 15000,
            'image_url' => 'https://images.unsplash.com/photo-1609126953519-7f4a5b1e9e58?w=600&q=80',
        ],
        (object) [
            'name' => 'Risol Mayo',
            'description' => 'Risol isi mayo dan smoked beef, dibalut tepung roti yang renyah',
            'price' => 3000,
            'image_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=600&q=80',
        ],
        (object) [
            'name' => 'Kopi Susu',
            'description' => 'Kopi susu kekinian dengan campuran espresso dan susu segar',
            'price' => 12000,
            'image_url' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=600&q=80',
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

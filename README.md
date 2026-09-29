# DcemilinYuk — Laravel

Website katalog cemilan dan minuman dari pedagang kecil lokal Indonesia. Konversi dari versi [React](https://github.com/ZhyperReyz/dcemilinyuk-web) ke **Laravel + Blade** dengan animasi yang sama persis.

## Tech Stack

- **Laravel** + Blade templating
- **Vite** + laravel-vite-plugin — build tool asset
- **GSAP** + ScrollTrigger — animasi scroll
- **Lenis** — smooth scroll
- **SQLite** (default) / MySQL — database produk
- **Vanilla CSS** — custom properties, dark theme

## Fitur & Animasi

| Section | Animasi |
|---------|---------|
| **Preloader** | Counter animasi 000-100 + curtain reveal |
| **Navbar** | Horizontal fixed, active underline, scroll blur effect |
| **Hero** | Word-by-word text reveal + parallax background |
| **Categories** | Scroll reveal cards |
| **Products** | Filter tab kategori + WA order button (data dari DB) |
| **PeelReveal** | Horizontal bars peel away on scroll |
| **Featured** | Best seller cards dengan hover lift |
| **SpecialtyDrinks** | Text scatter on hover |
| **Gallery** | Clip-path image reveals |
| **HorizontalScroll** | Horizontal gallery dengan GSAP pin + scroll |
| **About** | Clip-path reveal + stat counter animation |
| **HowToOrder** | Step cards dengan staggered reveal |
| **Testimonials** | Auto-rotating quotes |
| **CTA** | Scale-in WhatsApp banner |

## Data Produk (dari database)

```
CEMILAN    — Risol Mayo, Siomay, Lumpiah, Piscok, Sosis Bakar, Tahu Gejrot
MINUMAN    — Es Teh Tarik, Kopi Susu, Es Kepiting
MAKANAN    — Lemper Ayam, Nasi Uduk, Chicken Katsu, Bakso Mercon
KUE        — Bolen Pisang, Donat Kentang
FROZEN     — Sweet Potato Fries
```

Data tidak lagi hardcoded seperti versi React — produk diambil dari tabel `products` via `Product` model dan dirender dengan `@foreach` di Blade.

## Instalasi

```bash
# 1. Install dependency PHP
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. (Opsional) ganti driver database di .env, default sqlite
# DB_CONNECTION=sqlite

# 4. Migration + seeder (16 produk)
php artisan migrate --seed

# 5. Install dependency npm
npm install

# 6. Build asset produksi
npm run build
```

## Menjalankan

```bash
# Dev server Laravel + Vite (hot reload blade & asset)
composer dev
# atau manual:
php artisan serve
npm run dev

# Build untuk production
npm run build
```

Buka `http://127.0.0.1:8000`.

> **Catatan Windows/XAMPP:** bila CLI PHP tidak memuat `php.ini` (ekstensi `mbstring`, `openssl`, `pdo_sqlite` tidak aktif), jalankan dengan config tambahan yang ada di root workspace:
> ```bash
> php -c ../php-cli.ini artisan serve
> ```

## Konfigurasi WhatsApp

Nomor WA tujuan order diatur lewat config/env (lihat `config/dcemilinyuk.php`):

```env
DCEMILINYUK_WA_NUMBER=6281234567890
```

Tombol **"Pesan via WA"** di tiap produk otomatis membuat link:

```
https://wa.me/6281234567890?text=Halo, saya ingin pesan: *<Nama Produk>* Harga: Rp XX.000 ...
```

Bila kolom `whatsapp_order_link` di tabel produk diisi manual, link itu yang dipakai (meng-override generate otomatis).

## Struktur Project

```
dcemilinyuk-laravel/
├── app/
│   ├── Http/Controllers/
│   │   └── HomeController.php        — kirim data produk ke view home
│   ├── Models/
│   │   └── Product.php               — model + metadata kategori + accessor badge/harga/WA
│   └── Providers/
│       └── DcemilinyukServiceProvider.php
├── config/
│   └── dcemilinyuk.php               — nomor WA & nama brand
├── database/
│   ├── migrations/
│   │   └── xxxx_create_products_table.php
│   └── seeders/
│       └── ProductSeeder.php         — 16 produk dari data versi React
├── public/
│   ├── favicon.svg
│   └── images/                       — foto produk & galeri
│       └── products/
├── resources/
│   ├── css/
│   │   └── app.css                   — global tokens, reset, utilities (dari styles/index.css)
│   ├── js/
│   │   ├── app.js                    — entry point, init semua section & animasi
│   │   ├── lenis-init.js             — Lenis + sync GSAP ScrollTrigger (dari useLenis.ts)
│   │   └── scroll-reveal.js          — helper reveal reusable (dari useScrollReveal.ts)
│   └── views/
│       ├── home.blade.php            — merangkai semua partial
│       ├── layouts/
│       │   └── app.blade.php         — head, fonts, body wrapper
│       └── partials/
│           ├── preloader.blade.php
│           ├── navigation.blade.php
│           ├── hero.blade.php
│           ├── categories.blade.php
│           ├── products.blade.php
│           ├── peel-reveal.blade.php
│           ├── featured.blade.php
│           ├── specialty-drinks.blade.php
│           ├── gallery.blade.php
│           ├── horizontal-scroll.blade.php
│           ├── about.blade.php
│           ├── how-to-order.blade.php
│           ├── testimonials.blade.php
│           ├── cta.blade.php
│           └── footer.blade.php
├── routes/
│   └── web.php                       — GET / → HomeController@index
└── vite.config.js                    — laravel-vite-plugin, input css + js
```

## Urutan Section

Sesuai README versi React:

Preloader → Navbar → Hero → Categories → Products → PeelReveal → Featured → SpecialtyDrinks → Gallery → HorizontalScroll → About → HowToOrder → Testimonials → CTA → Footer

## Brand Guidelines

- **Font Display:** Playfair Display (Google Fonts)
- **Font Body:** Inter (Google Fonts)
- **Accent Color:** `#c8956c` (warm brown)
- **Background:** `#0a0a0a` (near black)
- **WhatsApp:** `#25D366`

## Format & Konvensi

- Harga dalam format **Rp XX.000** (accessor `formatted_price`)
- Badge: `best_seller` → Best Seller, `baru` → Baru, `pedas` → Pedas
- Kolom `category` enum: `CEMILAN` / `MINUMAN` / `MAKANAN` / `KUE` / `FROZEN`
- Pesan via WhatsApp langsung dari halaman produk

## Gambar Produk

Seeder mengisi `image_path` seperti `products/risol-mayo.jpg`. Letakkan foto di:

```
public/images/products/risol-mayo.jpg
public/images/products/siomay.jpg
... dst
```

Bila foto belum ada, otomatis fallback ke placeholder brand (`placehold.co` warna `#0a0a0a` / `#c8956c`).

## Catatan Porting dari React

| React | Laravel |
|-------|---------|
| `hooks/useLenis.ts` | `resources/js/lenis-init.js` |
| `hooks/useScrollReveal.ts` | `resources/js/scroll-reveal.js` (berbasis `data-attributes`) |
| `data/products.ts` | `app/Models/Product.php` + `ProductSeeder` |
| `App.tsx` (komposisi section) | `resources/views/home.blade.php` |
| Komponen `.tsx` | `resources/views/partials/*.blade.php` |
| `styles/index.css` | `resources/css/app.css` |

## License

(c) 2026 DcemilinYuk. All rights reserved.

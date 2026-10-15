# D'Cemilinyuk

Toko online cemilan & minuman (**cemilan, minuman, makanan, kue, frozen**) yang dibangun dengan Laravel 12.

Pelanggan menjelajahi katalog, membuka formulir pemesanan, lalu menyelesaikan checkout
multi-step: isi data pengiriman → pilih metode pembayaran (transfer bank / QRIS / COD)
→ halaman pesanan berhasil dengan tombol konfirmasi WhatsApp.

Sisi admin menyediakan dashboard metrik, pengelolaan produk & kategori, manajemen stok,
Riwayat pesanan, pengaturan toko, serta (khusus super admin) pengelolaan pengguna.

---

## Stack

| Bagian | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.4) |
| Database | SQLite (default, mudah untuk dev) |
| Frontend | Blade + CSS/JS kustom, Vite |
| Autentikasi | Laravel Auth (`web` guard) + session |
| Testing | PHPUnit / Pest (133 test) |

---

## Menjalankan

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed   # buat tabel + data contoh
php artisan storage:link           # supaya upload gambar produk bisa diakses

php artisan serve                  # http://127.0.0.1:8000
npm run dev                        # aset frontend (opsional, sudah ada build di public/)
```

### Akun contoh (dari seeder)

| Email | Password | Role |
|---|---|---|
| `superadmin@dcemilinyuk.id` | `password` | Super Admin |
| `admin@dcemilinyuk.id` | `password` | Admin |
| `pelanggan@dcemilinyuk.id` | `password` | Pelanggan |

> ⚠️ **Ganti password seeder sebelum deploy ke server publik.**
> Ganti juga `DCEMILINYUK_WA_NUMBER` dan rekening bank di **Pengaturan Toko**.

---

## Struktur

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # auth, dashboard, produk, kategori, stok, pesanan, pengaturan, pengguna
│   │   ├── Api/            # endpoint JSON: produk, kategori, pesanan, settings
│   │   ├── CheckoutController.php
│   │   └── HomeController.php
│   └── Middleware/
│       ├── AdminSession.php        # guard area admin (login + role + aktif)
│       ├── EnsureSuperAdmin.php    # khusus super admin
│       └── ForceJsonResponse.php   # /api selalu JSON, termasuk error
├── Models/
│   ├── Category.php  Order.php  Product.php  Setting.php  User.php
└── Services/
    └── OrderService.php  # hitung ongkir/total, nomor pesanan, kurangi stok

database/
├── factories/  ProductFactory, CategoryFactory, UserFactory
├── migrations/ users, products, orders, categories, settings
└── seeders/    Setting, Category, Product, User, Order

resources/views/
├── home.blade.php + partials/   # landing page 10 seksi
├── checkout/                   # payment, success
└── admin/                      # dashboard, auth, produk, kategori, stok, pesanan, pengaturan, pengguna

routes/
├── web.php   # storefront + panel admin
└── api.php   # JSON API
```

---

## Database

| Tabel | Isi |
|---|---|
| `users` | Admin & pelanggan. Kolom `role` (`user`/`admin`/`superadmin`), `is_active`, `last_login_at` |
| `products` | Katalog. `category` menyimpan **kode** kategori, bukan enum, supaya kategori bebas ditambah |
| `categories` | Kategori (code, nama, ikon, gambar, urutan, aktif/tidak) |
| `orders` | Pesanan. `product_name`/`unit_price` disimpan sebagai snapshot agar riwayat tidak berubah |
| `settings` | Konfigurasi toko (key-value): brand, ongkir, rekening, toggle COD |

> Hubungan `products.category` → `categories.code` dibuat lewat relasi
> `Product::categoryInfo()`. Namanya memakai akhiran *Info* supaya tidak bentrok
> dengan kolom string `category` yang juga dipakai sebagai kode kategori untuk filter.

---

## API JSON

Semua endpoint di bawah `/api` **selalu** merespons JSON (matahaleven tanpa header `Accept`).

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/api/products` | Katalog. Query: `category`, `search`, `sort` (`newest`, `price_asc`, `price_desc`, `best_seller`, `name`), `per_page` (maks 60) |
| `GET` | `/api/products/{id}` | Detail satu produk |
| `GET` | `/api/categories` | Kategori aktif, terurut |
| `GET` | `/api/settings` | Pengaturan publik + metode pembayaran yang aktif |
| `POST` | `/api/orders` | Buat pesanan (validasi stok, hitung ongkir otomatis) |
| `GET` | `/api/orders/{orderNumber}` | Lacak pesanan. Wajib menyertakan `?email=` atau `?phone=` sebagai verifikasi |

Contoh:

```bash
curl "http://localhost:8000/api/products?sort=best_seller&per_page=5"

curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"customer_name":"Budi","customer_phone":"081234567890",
       "shipping_address":"Jl. Merdeka 10","items_count":3}'
```

Bentuk respons produk konsisten antara `index` dan `show`:

```json
{
  "id": 1, "sku": "DC-001", "name": "Risol Mayo",
  "category": "Cemilan", "category_code": "CEMILAN",
  "price": 3000, "formatted_price": "Rp 3.000",
  "rating": 4.9, "sold": 2100, "stock": 45,
  "badge": "Best Seller",
  "image_url": "http://localhost:8000/images/products/risol-mayo.webp",
  "whatsapp_order_url": "https://wa.me/6281234567890?text=..."
}
```

---

## Alur Checkout

1. **Simpan pesanan** — `OrderService` memvalidasi stok, menghitung ongkir
   (gratis di atas `free_shipping_threshold`), biaya layanan, dan total,
   lalu membuat nomor unik `ORD-YYYYMMDD-XXXX`.
2. **Halaman pembayaran** — rekening diambil dari tabel `settings`;
   bank yang nomornya kosong otomatis disembunyikan.
3. **Konfirmasi** — `payment_status` berubah sesuai metode (COD tidak bisa dipilih
   kalau `cod_enabled` dimatikan) dan stok **dikurangi sekali saja**,
   bahkan kalau pembeli me-refresh halaman.
4. **Pesanan berhasil** — template pesan WhatsApp otomatis untuk konfirmasi ke admin.

---

## Manajemen Stok

`products` punya `stock` dan `min_stock`. Admin punya:

- **Restock** (`POST /admin/stock/{id}/restock`) — menambah stok; produk nonaktif otomatis aktif lagi.
- **Set manual** (`PUT /admin/stock/{id}`) — untuk koreksi; stok `0` otomatis menandai produk habis.
- **Status inventaris**: `Kritis` (≤ 0), `Perlu Restock` (≤ `min_stock`), `Aman`.

Produk **tidak bisa dihapus** kalau sudah punya riwayat pesanan — nonaktifkan sebagai gantinya.

---

## Keamanan

- Password admin di-hash otomatis (`password` cast), minimal 8 karakter.
- Rate limit login: **5 percobaan gagal** → dikunci 1 menit.
- Pesan error login sengaja dibuat generik agar tidak membocorkan info user.
- `AdminSession` memeriksa login **dan** role **dan** status aktif; akun nonaktif langsung
  di-logout paksa.
- Area kelola pengguna dibatasi middleware `superadmin`, dan super admin terakhir
  tidak bisa diturunkan, dinonaktifkan, atau dihapus.
- Upload gambar dibatasi 2 MB dan tipe `jpg/jpeg/png/webp`; file lama dihapus
  saat diganti.
- Produksi: pastikan `APP_DEBUG=false`.

---

## Testing

```bash
php artisan test                        # semua
php artisan test --testsuite=Unit       # model, accessor, scope, service
php artisan test --testsuite=Feature    # HTTP, auth, API, upload, checkout
```

Cakupan: katalog & filter API, pembuatan & pelacakan pesanan, alur checkout 4 langkah,
login admin (termasuk rate limit & akun nonaktif), CRUD produk + upload gambar,
manajemen stok, filter pesanan, pengaturan toko, dan pengaturan pengguna.

---

## Checklist Sebelum Deploy

- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] Ganti password semua akun seeder
- [ ] Isi rekening & nomor WhatsApp asli di **Pengaturan Toko**
- [ ] `APP_DEBUG=false` di `.env`
- [ ] `php artisan optimize` (config + route + view cache)
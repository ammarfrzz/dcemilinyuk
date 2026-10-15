<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const CATEGORY_CEMILAN = 'CEMILAN';
    public const CATEGORY_MINUMAN = 'MINUMAN';
    public const CATEGORY_MAKANAN = 'MAKANAN';
    public const CATEGORY_KUE = 'KUE';
    public const CATEGORY_FROZEN = 'FROZEN';

    /**
     * Metadata kategori untuk tab filter & section Categories
     * (port dari `categories` di data/products.ts versi React).
     */
    public const CATEGORIES = [
        self::CATEGORY_CEMILAN => [
            'name' => 'Cemilan',
            'icon' => 'fa-solid fa-cookie-bite',
            'image' => '/images/products/risol-mayo.webp',
            'description' => 'Aneka gorengan dan jajanan renyah untuk teman santai.',
        ],
        self::CATEGORY_MINUMAN => [
            'name' => 'Minuman',
            'icon' => 'fa-solid fa-mug-hot',
            'image' => '/images/products/es-teh-tarik.webp',
            'description' => 'Es teh tarik, kopi susu gula aren, dan es buah segar.',
        ],
        self::CATEGORY_MAKANAN => [
            'name' => 'Makanan',
            'icon' => 'fa-solid fa-utensils',
            'image' => '/images/products/nasi-uduk.webp',
            'description' => 'Makanan hangat yang mengenyangkan untuk kenyang banget.',
        ],
        self::CATEGORY_KUE => [
            'name' => 'Kue',
            'icon' => 'fa-solid fa-cake-candles',
            'image' => '/images/products/donat-kentang.webp',
            'description' => 'Kue dan pastry lembut, cocok untuk camilan manis.',
        ],
        self::CATEGORY_FROZEN => [
            'name' => 'Frozen',
            'icon' => 'fa-solid fa-snowflake',
            'image' => '/images/products/sweet-potato-fries.webp',
            'description' => 'Frozen food siap goreng, praktis untuk kapan saja.',
        ],
    ];

    /**
     * Map foto produk lokal WebP berkualitas tinggi
     */
    public const PRODUCT_LOCAL_IMAGES = [
        'Risol Mayo' => 'products/risol-mayo.webp',
        'Siomay' => 'products/siomay.webp',
        'Lumpiah' => 'products/lumpiah.webp',
        'Piscok' => 'products/piscok.webp',
        'Sosis Bakar' => 'products/sosis-bakar.webp',
        'Tahu Gejrot' => 'products/tahu-gejrot.webp',
        'Es Teh Tarik' => 'products/es-teh-tarik.webp',
        'Kopi Susu' => 'products/kopi-susu.webp',
        'Es Buah' => 'products/es-buah.webp',
        'Es Kepiting' => 'products/es-kepiting.webp',
        'Lemper Ayam' => 'products/lemper-ayam.webp',
        'Nasi Uduk' => 'products/nasi-uduk.webp',
        'Chicken Katsu' => 'products/chicken-katsu.webp',
        'Bakso Mercon' => 'products/bakso-mercon.webp',
        'Bolen Pisang' => 'products/bolen-pisang.webp',
        'Donat Kentang' => 'products/donat-kentang.webp',
        'Sweet Potato Fries' => 'products/sweet-potato-fries.webp',
    ];

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'badge',
        'image_path',
        'whatsapp_order_link',
        'rating',
        'sold',
        'available',
        'stock',
        'min_stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'sold' => 'integer',
            'rating' => 'decimal:1',
            'available' => 'boolean',
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('available', true)->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeBestSellers($query, int $limit = 8)
    {
        return $query->available()->orderByDesc('sold')->limit($limit)->get();
    }

    public function scopeCategory($query, ?string $category)
    {
        return $query->when($category, fn ($q) => $q->where('category', $category));
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn ($q) => $q->where('name', 'like', '%' . $term . '%'));
    }

    /**
     * Relasi ke tabel categories lewat kolom `category` (kode kategori).
     *
     * Nama method memakai akhiran Info supaya tidak bentrok dengan atribut
     * string `category` yang dipakai sebagai kode kategori.
     */
    public function categoryInfo(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category', 'code');
    }

    /** Relasi ke orders (produk ini pernah dipesan) */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Label badge untuk tampilan: best_seller → Best Seller, dst. */
    public function getBadgeLabelAttribute(): ?string
    {
        return match ($this->badge) {
            'best_seller' => 'Best Seller',
            'baru' => 'Baru',
            'pedas' => 'Pedas',
            default => null,
        };
    }

    /** Class CSS badge mengikuti versi React (badge--bestseller, badge--new, dst). */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->badge) {
            'best_seller' => 'badge--bestseller',
            'baru' => 'badge--new',
            'pedas' => 'badge--pedas',
            default => 'badge--popular',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getDisplayCategoryAttribute(): string
    {
        // Ambil dari relasi categoryInfo jika sudah di-eager load, fallback ke constant CATEGORIES
        if ($this->relationLoaded('categoryInfo') && $this->categoryInfo) {
            return $this->categoryInfo->name;
        }

        return self::CATEGORIES[$this->category]['name'] ?? $this->category;
    }

    public function getSkuAttribute(): string
    {
        return 'DC-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }

    public function getStockAttribute(): int
    {
        return (int) ($this->attributes['stock'] ?? 45);
    }

    public function getMinStockAttribute(): int
    {
        return (int) ($this->attributes['min_stock'] ?? 10);
    }

    /** True kalau stok menipis (kurang dari min_stock) dan masih tersedia. */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /** Label status inventaris untuk halaman admin Manage Stock. */
    public function getStockStatusLabelAttribute(): string
    {
        return match (true) {
            $this->stock <= 0 => 'Kritis',
            $this->stock <= $this->min_stock => 'Perlu Restock',
            default => 'Aman',
        };
    }

    public function getStockStatusClassAttribute(): string
    {
        return match (true) {
            $this->stock <= 0 => 'badge-danger',
            $this->stock <= $this->min_stock => 'badge-warning',
            default => 'badge-success',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->available || $this->stock <= 0) {
            return 'Habis';
        }

        return $this->is_low_stock ? 'Stok Menipis' : 'Tersedia';
    }

    public function getStatusClassAttribute(): string
    {
        if (! $this->available || $this->stock <= 0) {
            return 'badge-danger';
        }

        return $this->is_low_stock ? 'badge-warning' : 'badge-success';
    }

    /** URL gambar: mengutamakan image_path database/input, lalu mapping lokal, lalu kategori */
    public function getImageUrlAttribute(): string
    {
        // 1. Prioritas Utama: Nilai dari database/input ($this->image_path)
        if (!empty($this->image_path)) {
            // Jika URL eksternal (http/https)
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            // Jika path langsung di folder public/ (misal: "images/products/foo.webp" atau "storage/products/foo.webp")
            $cleanPath = ltrim($this->image_path, '/');
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            // Jika path relatif di dalam public/images/ (misal: "products/foo.webp")
            if (file_exists(public_path('images/' . $cleanPath))) {
                return asset('images/' . $cleanPath);
            }
        }

        // 2. Fallback: Cek mapping lokal berdasarkan nama produk jika image_path tidak ditemukan
        if (isset(self::PRODUCT_LOCAL_IMAGES[$this->name])) {
            $localRel = self::PRODUCT_LOCAL_IMAGES[$this->name];
            if (file_exists(public_path('images/' . $localRel))) {
                return asset('images/' . $localRel);
            }
        }

        // 3. Fallback: Gambar kategori dari database
        if ($this->relationLoaded('categoryInfo') && $this->categoryInfo && $this->categoryInfo->image_path) {
            return asset($this->categoryInfo->image_path);
        }

        // 4. Fallback: Gambar kategori dari constant
        if (isset(self::CATEGORIES[$this->category]['image'])) {
            return asset(self::CATEGORIES[$this->category]['image']);
        }

        return asset('images/products/risol-mayo.webp');
    }

    /**
     * Link order WhatsApp: kolom whatsapp_order_link kalau diisi manual,
     * kalau tidak, generate otomatis berisi nama produk + harga (requirement #2).
     */
    public function getWhatsappOrderUrlAttribute(): string
    {
        if ($this->whatsapp_order_link) {
            return $this->whatsapp_order_link;
        }

        $number = config('dcemilinyuk.wa_number', '6281234567890');

        $message = sprintf(
            "Halo, saya ingin pesan:\n\n*%s*\nHarga: %s\n\nMohon info detail dan cara pembayarannya. Terima kasih!",
            $this->name,
            $this->formatted_price
        );

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}
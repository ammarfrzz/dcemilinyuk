<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'sold' => 'integer',
            'rating' => 'decimal:1',
            'available' => 'boolean',
        ];
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    public function scopeBestSellers($query, int $limit = 8)
    {
        return $query->available()->orderByDesc('sold')->limit($limit)->get();
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

        // 3. Fallback: Gambar kategori
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

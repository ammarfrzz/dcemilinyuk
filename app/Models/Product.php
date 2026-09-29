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
            'description' => 'Aneka gorengan dan jajanan renyah untuk teman santai.',
        ],
        self::CATEGORY_MINUMAN => [
            'name' => 'Minuman',
            'icon' => 'fa-solid fa-mug-hot',
            'description' => 'Es teh, kopi susu, dan minuman segar lainnya.',
        ],
        self::CATEGORY_MAKANAN => [
            'name' => 'Makanan',
            'icon' => 'fa-solid fa-utensils',
            'description' => 'Makanan hangat yang mengenyangkan untuk kenyang banget.',
        ],
        self::CATEGORY_KUE => [
            'name' => 'Kue',
            'icon' => 'fa-solid fa-cake-candles',
            'description' => 'Kue dan pastry lembut, cocok untuk camilan manis.',
        ],
        self::CATEGORY_FROZEN => [
            'name' => 'Frozen',
            'icon' => 'fa-solid fa-snowflake',
            'description' => 'Frozen food siap goreng, praktis untuk kapan saja.',
        ],
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

    /** URL gambar: pakai image_path kalau ada, fallback placeholder brand. */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('images/' . $this->image_path);
        }

        return 'https://placehold.co/400x300/0a0a0a/c8956c?text=' . rawurlencode($this->name);
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

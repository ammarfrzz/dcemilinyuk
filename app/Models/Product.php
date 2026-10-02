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
            'image' => 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=600&auto=format&fit=crop&q=80',
            'description' => 'Aneka gorengan dan jajanan renyah untuk teman santai.',
        ],
        self::CATEGORY_MINUMAN => [
            'name' => 'Minuman',
            'icon' => 'fa-solid fa-mug-hot',
            'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&auto=format&fit=crop&q=80',
            'description' => 'Es teh, kopi susu, dan minuman segar lainnya.',
        ],
        self::CATEGORY_MAKANAN => [
            'name' => 'Makanan',
            'icon' => 'fa-solid fa-utensils',
            'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=600&auto=format&fit=crop&q=80',
            'description' => 'Makanan hangat yang mengenyangkan untuk kenyang banget.',
        ],
        self::CATEGORY_KUE => [
            'name' => 'Kue',
            'icon' => 'fa-solid fa-cake-candles',
            'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&auto=format&fit=crop&q=80',
            'description' => 'Kue dan pastry lembut, cocok untuk camilan manis.',
        ],
        self::CATEGORY_FROZEN => [
            'name' => 'Frozen',
            'icon' => 'fa-solid fa-snowflake',
            'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600&auto=format&fit=crop&q=80',
            'description' => 'Frozen food siap goreng, praktis untuk kapan saja.',
        ],
    ];

    /**
     * External fallback images dari Unsplash berkualitas tinggi
     * untuk setiap menu cemilan dan minuman lokal DcemilinYuk.
     */
    public const PRODUCT_EXTERNAL_IMAGES = [
        'Risol Mayo' => 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=600&auto=format&fit=crop&q=80',
        'Siomay' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=600&auto=format&fit=crop&q=80',
        'Lumpiah' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
        'Piscok' => 'https://images.unsplash.com/photo-1559620192-032c4bc4674e?w=600&auto=format&fit=crop&q=80',
        'Sosis Bakar' => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=600&auto=format&fit=crop&q=80',
        'Tahu Gejrot' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
        'Es Teh Tarik' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&auto=format&fit=crop&q=80',
        'Kopi Susu' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80',
        'Es Kepiting' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&auto=format&fit=crop&q=80',
        'Lemper Ayam' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?w=600&auto=format&fit=crop&q=80',
        'Nasi Uduk' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=600&auto=format&fit=crop&q=80',
        'Chicken Katsu' => 'https://images.unsplash.com/photo-1625813506062-0aeb1d7a094b?w=600&auto=format&fit=crop&q=80',
        'Bakso Mercon' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=600&auto=format&fit=crop&q=80',
        'Bolen Pisang' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
        'Donat Kentang' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&auto=format&fit=crop&q=80',
        'Sweet Potato Fries' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600&auto=format&fit=crop&q=80',
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

    /** URL gambar: pakai image_path kalau ada dan file ada di disk / URL eksternal, fallback ke Unsplash eksternal berkualitas tinggi. */
    public function getImageUrlAttribute(): string
    {
        // 1. Jika image_path sudah berupa URL eksternal lengkap
        if ($this->image_path && (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://'))) {
            return $this->image_path;
        }

        // 2. Jika file lokal benar-benar ada di public/images
        if ($this->image_path && file_exists(public_path('images/' . $this->image_path))) {
            return asset('images/' . $this->image_path);
        }

        // 3. Ambil dari mapping foto eksternal berkualitas tinggi per produk
        if (isset(self::PRODUCT_EXTERNAL_IMAGES[$this->name])) {
            return self::PRODUCT_EXTERNAL_IMAGES[$this->name];
        }

        // 4. Fallback ke gambar kategori
        if (isset(self::CATEGORIES[$this->category]['image'])) {
            return self::CATEGORIES[$this->category]['image'];
        }

        return 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80';
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

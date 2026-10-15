<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'icon',
        'image_path',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->active()->orderBy('sort_order')->orderBy('name');
    }

    /**
     * URL gambar kategori, dengan fallback ke metadata hardcoded Product::CATEGORIES.
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            $cleanPath = ltrim($this->image_path, '/');

            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            if (file_exists(public_path('images/' . $cleanPath))) {
                return asset('images/' . $cleanPath);
            }
        }

        $fallback = Product::CATEGORIES[$this->code]['image'] ?? null;

        return $fallback ? asset($fallback) : asset('images/products/risol-mayo.webp');
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products_count ?? $this->products()->available()->count();
    }
}
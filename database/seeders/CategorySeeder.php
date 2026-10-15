<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori dari metadata yang sama dengan Product::CATEGORIES supaya
     * data tetap konsisten antara hardcoded fallback dan database.
     */
    public function run(): void
    {
        $sort = 0;

        foreach (Product::CATEGORIES as $code => $meta) {
            Category::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $meta['name'],
                    'slug' => Str::slug($meta['name']),
                    'icon' => $meta['icon'],
                    'image_path' => $meta['image'],
                    'description' => $meta['description'],
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]
            );
        }
    }
}
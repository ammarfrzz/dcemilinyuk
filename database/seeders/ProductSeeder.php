<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Data produk port dari README versi React:
     * CEMILAN — Risol Mayo, Siomay, Lumpiah, Piscok, Sosis Bakar, Tahu Gejrot
     * MINUMAN — Es Teh Tarik, Kopi Susu, Es Kepiting
     * MAKANAN — Lemper Ayam, Nasi Uduk, Chicken Katsu, Bakso Mercon
     * KUE     — Bolen Pisang, Donat Kentang
     * FROZEN  — Sweet Potato Fries
     */
    public function run(): void
    {
        $products = [
            // ── CEMILAN ─────────────────────────────
            [
                'name' => 'Risol Mayo',
                'category' => 'CEMILAN',
                'description' => 'Risol isi mayo dan smoked beef, dibalut tepung roti yang renyah.',
                'price' => 3000,
                'badge' => 'best_seller',
                'image_path' => 'products/risol-mayo.jpg',
                'rating' => 4.9,
                'sold' => 2100,
            ],
            [
                'name' => 'Siomay',
                'category' => 'CEMILAN',
                'description' => 'Siomay ayam steamed dengan saus kacang gurih dan pedas.',
                'price' => 5000,
                'image_path' => 'products/siomay.jpg',
                'rating' => 4.7,
                'sold' => 1350,
            ],
            [
                'name' => 'Lumpiah',
                'category' => 'CEMILAN',
                'description' => 'Lumpiah goreng isi sayuran segar, renyah di luar juicy di dalam.',
                'price' => 4000,
                'image_path' => 'products/lumpiah.jpg',
                'rating' => 4.6,
                'sold' => 980,
            ],
            [
                'name' => 'Piscok',
                'category' => 'CEMILAN',
                'description' => 'Pisang coklat goreng crispy dengan lelehan coklat legit.',
                'price' => 3500,
                'badge' => 'baru',
                'image_path' => 'products/piscok.jpg',
                'rating' => 4.8,
                'sold' => 1150,
            ],
            [
                'name' => 'Sosis Bakar',
                'category' => 'CEMILAN',
                'description' => 'Sosis bakar juicy dengan bumbu spesial dan saus mayo.',
                'price' => 8000,
                'image_path' => 'products/sosis-bakar.jpg',
                'rating' => 4.6,
                'sold' => 870,
            ],
            [
                'name' => 'Tahu Gejrot',
                'category' => 'CEMILAN',
                'description' => 'Tahu goreng dengan kuah gejrot pedas manis khas Cirebon.',
                'price' => 5000,
                'badge' => 'pedas',
                'image_path' => 'products/tahu-gejrot.jpg',
                'rating' => 4.5,
                'sold' => 640,
            ],

            // ── MINUMAN ─────────────────────────────
            [
                'name' => 'Es Teh Tarik',
                'category' => 'MINUMAN',
                'description' => 'Teh tarik premium dengan rasa creamy dan manis yang pas.',
                'price' => 8000,
                'badge' => 'best_seller',
                'image_path' => 'products/es-teh-tarik.jpg',
                'rating' => 4.9,
                'sold' => 1750,
            ],
            [
                'name' => 'Kopi Susu',
                'category' => 'MINUMAN',
                'description' => 'Kopi susu kekinian dengan campuran espresso dan susu segar.',
                'price' => 12000,
                'image_path' => 'products/kopi-susu.jpg',
                'rating' => 4.8,
                'sold' => 1420,
            ],
            [
                'name' => 'Es Kepiting',
                'category' => 'MINUMAN',
                'description' => 'Minuman segar khas pesisir dengan sensasi asam manis unik.',
                'price' => 10000,
                'badge' => 'baru',
                'image_path' => 'products/es-kepiting.jpg',
                'rating' => 4.7,
                'sold' => 760,
            ],

            // ── MAKANAN ─────────────────────────────
            [
                'name' => 'Lemper Ayam',
                'category' => 'MAKANAN',
                'description' => 'Lemper ketan isi ayam suwir pedas manis, dibalut daun pisang.',
                'price' => 6000,
                'image_path' => 'products/lemper-ayam.jpg',
                'rating' => 4.7,
                'sold' => 690,
            ],
            [
                'name' => 'Nasi Uduk',
                'category' => 'MAKANAN',
                'description' => 'Nasi uduk gurih dengan lauk pilihan, cocok untuk sarapan.',
                'price' => 15000,
                'badge' => 'best_seller',
                'image_path' => 'products/nasi-uduk.jpg',
                'rating' => 4.8,
                'sold' => 1240,
            ],
            [
                'name' => 'Chicken Katsu',
                'category' => 'MAKANAN',
                'description' => 'Katsu ayam crispy dengan saus katsu manis gurih.',
                'price' => 18000,
                'image_path' => 'products/chicken-katsu.jpg',
                'rating' => 4.7,
                'sold' => 1105,
            ],
            [
                'name' => 'Bakso Mercon',
                'category' => 'MAKANAN',
                'description' => 'Bakso isi cabai rawit dengan kuah kaldu pedas menggigit.',
                'price' => 12000,
                'badge' => 'pedas',
                'image_path' => 'products/bakso-mercon.jpg',
                'rating' => 4.6,
                'sold' => 950,
            ],

            // ── KUE ─────────────────────────────────
            [
                'name' => 'Bolen Pisang',
                'category' => 'KUE',
                'description' => 'Bolen pisang homemade dengan kulit renyah dan isian pisang melimpah.',
                'price' => 15000,
                'badge' => 'best_seller',
                'image_path' => 'products/bolen-pisang.jpg',
                'rating' => 4.9,
                'sold' => 1980,
            ],
            [
                'name' => 'Donat Kentang',
                'category' => 'KUE',
                'description' => 'Donat kentang lembut dengan aneka topping manis.',
                'price' => 5000,
                'image_path' => 'products/donat-kentang.jpg',
                'rating' => 4.6,
                'sold' => 830,
            ],

            // ── FROZEN ──────────────────────────────
            [
                'name' => 'Sweet Potato Fries',
                'category' => 'FROZEN',
                'description' => 'Ubi jalar ungu potong renyah, frozen food siap goreng.',
                'price' => 20000,
                'image_path' => 'products/sweet-potato-fries.jpg',
                'rating' => 4.5,
                'sold' => 430,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}

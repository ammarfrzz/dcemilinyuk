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
                'description' => 'Risol isi mayo dan smoked beef gurih, dibalut tepung roti renyah keemasan.',
                'price' => 3000,
                'badge' => 'best_seller',
                'image_path' => 'products/risol-mayo.webp',
                'rating' => 4.9,
                'sold' => 2100,
            ],
            [
                'name' => 'Siomay',
                'category' => 'CEMILAN',
                'description' => 'Siomay ayam steamed dengan saus kacang gurih pedas dan perasan jeruk limau.',
                'price' => 5000,
                'image_path' => 'products/siomay.webp',
                'rating' => 4.7,
                'sold' => 1350,
            ],
            [
                'name' => 'Lumpiah',
                'category' => 'CEMILAN',
                'description' => 'Lumpiah goreng isi sayuran segar, renyah di luar juicy di dalam.',
                'price' => 4000,
                'image_path' => 'products/lumpiah.webp',
                'rating' => 4.6,
                'sold' => 980,
            ],
            [
                'name' => 'Piscok',
                'category' => 'CEMILAN',
                'description' => 'Pisang coklat goreng crispy dengan lelehan coklat legit lumer.',
                'price' => 3500,
                'badge' => 'baru',
                'image_path' => 'products/piscok.webp',
                'rating' => 4.8,
                'sold' => 1150,
            ],
            [
                'name' => 'Sosis Bakar',
                'category' => 'CEMILAN',
                'description' => 'Sosis bakar juicy dengan bumbu spesial BBQ dan saus mayo.',
                'price' => 8000,
                'image_path' => 'products/sosis-bakar.webp',
                'rating' => 4.6,
                'sold' => 870,
            ],
            [
                'name' => 'Tahu Gejrot',
                'category' => 'CEMILAN',
                'description' => 'Tahu pong goreng dengan siraman kuah gejrot pedas asam manis khas Cirebon.',
                'price' => 5000,
                'badge' => 'pedas',
                'image_path' => 'products/tahu-gejrot.webp',
                'rating' => 4.5,
                'sold' => 640,
            ],

            // ── MINUMAN ─────────────────────────────
            [
                'name' => 'Es Teh Tarik',
                'category' => 'MINUMAN',
                'description' => 'Teh tarik premium dingin dengan buih melimpah dan rasa creamy yang pas.',
                'price' => 8000,
                'badge' => 'best_seller',
                'image_path' => 'products/es-teh-tarik.webp',
                'rating' => 4.9,
                'sold' => 1750,
            ],
            [
                'name' => 'Kopi Susu',
                'category' => 'MINUMAN',
                'description' => 'Kopi susu gula aren kekinian dengan campuran espresso mantap dan susu segar.',
                'price' => 12000,
                'image_path' => 'products/kopi-susu.webp',
                'rating' => 4.8,
                'sold' => 1420,
            ],
            [
                'name' => 'Es Buah',
                'category' => 'MINUMAN',
                'description' => 'Minuman segar aneka buah tropis manis dengan sirup dan susu creamy menyegarkan.',
                'price' => 10000,
                'badge' => 'baru',
                'image_path' => 'products/es-buah.webp',
                'rating' => 4.7,
                'sold' => 760,
            ],

            // ── MAKANAN ─────────────────────────────
            [
                'name' => 'Lemper Ayam',
                'category' => 'MAKANAN',
                'description' => 'Lemper ketan isi ayam suwir pedas manis gurih, dibalut daun pisang.',
                'price' => 6000,
                'image_path' => 'products/lemper-ayam.webp',
                'rating' => 4.7,
                'sold' => 690,
            ],
            [
                'name' => 'Nasi Uduk',
                'category' => 'MAKANAN',
                'description' => 'Nasi uduk gurih Betawi komplit dengan lauk pilihan, cocok untuk sarapan dan makan siang.',
                'price' => 15000,
                'badge' => 'best_seller',
                'image_path' => 'products/nasi-uduk.webp',
                'rating' => 4.8,
                'sold' => 1240,
            ],
            [
                'name' => 'Chicken Katsu',
                'category' => 'MAKANAN',
                'description' => 'Katsu ayam fillet tebal super crispy di luar dan juicy di dalam, disajikan hangat dengan saus katsu gurih manis spesial.',
                'price' => 18000,
                'image_path' => 'products/chicken-katsu.webp',
                'rating' => 4.7,
                'sold' => 1105,
            ],
            [
                'name' => 'Bakso Mercon',
                'category' => 'MAKANAN',
                'description' => 'Bakso isi cabai rawit dengan kuah kaldu pedas menggigit.',
                'price' => 12000,
                'badge' => 'pedas',
                'image_path' => 'products/bakso-mercon.webp',
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
                'image_path' => 'products/bolen-pisang.webp',
                'rating' => 4.9,
                'sold' => 1980,
            ],
            [
                'name' => 'Donat Kentang',
                'category' => 'KUE',
                'description' => 'Donat kentang jadul tekstur super lembut dan empuk, ditaburi gula halus salju dan aneka topping coklat keju nikmat.',
                'price' => 5000,
                'image_path' => 'products/donat-kentang.webp',
                'rating' => 4.6,
                'sold' => 830,
            ],

            // ── FROZEN ──────────────────────────────
            [
                'name' => 'Sweet Potato Fries',
                'category' => 'FROZEN',
                'description' => 'Ubi jalar ungu potong renyah, frozen food siap goreng.',
                'price' => 20000,
                'image_path' => 'products/sweet-potato-fries.webp',
                'rating' => 4.5,
                'sold' => 430,
            ],
        ];

        // Hapus produk lama yang tidak ada di daftar 16 produk resmi
        $validNames = array_column($products, 'name');
        Product::whereNotIn('name', $validNames)->delete();

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}

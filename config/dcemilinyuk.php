<?php

// Konfigurasi brand DcemilinYuk
//
// Nilai di sini hanya FALLBACK. Nilai operasional (ongkir, rekening, nama brand,
// nomor WhatsApp, dll.) disimpan di tabel `settings` dan bisa diubah dari
// panel admin > Pengaturan Toko.
return [
    /**
     * Nomor WhatsApp tujuan order (format internasional tanpa +).
     * Dipakai untuk generate link https://wa.me/{number}?text=...
     */
    'wa_number' => env('DCEMILINYUK_WA_NUMBER', '6281234567890'),

    /**
     * Nama brand yang dipakai di layout & footer.
     */
    'brand' => env('DCEMILINYUK_BRAND', "D'Cemilinyuk"),

    /**
     * Biaya default (dipakai kalau tabel `settings` belum diisi).
     */
    'shipping_fee' => (int) env('DCEMILINYUK_SHIPPING_FEE', 10000),
    'service_fee' => (int) env('DCEMILINYUK_SERVICE_FEE', 1000),
    'free_shipping_threshold' => (int) env('DCEMILINYUK_FREE_SHIPPING_THRESHOLD', 150000),

    /**
     * Batas upload gambar produk (kilobyte).
     */
    'image_max_kb' => (int) env('DCEMILINYUK_IMAGE_MAX_KB', 2048),

    /**
     * Rentang IP yang bebas rate limit untuk API (kosong = semua IP dibatasi).
     */
    'api_rate_limit' => (int) env('DCEMILINYUK_API_RATE_LIMIT', 60),
];
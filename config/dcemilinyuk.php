<?php

// Konfigurasi brand DcemilinYuk
return [
    /**
     * Nomor WhatsApp tujuan order (format internasional tanpa +).
     * Dipakai untuk generate link https://wa.me/{number}?text=...
     */
    'wa_number' => env('DCEMILINYUK_WA_NUMBER', '6281234567890'),

    /**
     * Nama brand yang dipakai di layout & footer.
     */
    'brand' => env('DCEMILINYUK_BRAND', 'DcemilinYuk'),
];

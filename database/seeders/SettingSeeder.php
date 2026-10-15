<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Nilai bawaan pengaturan toko. Semua bisa diubah dari panel admin.
     */
    public const DEFAULTS = [
        'brand_name' => ["D'Cemilinyuk", 'general'],
        'brand_tagline' => ['Cemilan & Minuman Favorit Keluarga', 'general'],
        'wa_number' => ['6281234567890', 'general'],
        'support_email' => ['halo@dcemilinyuk.id', 'general'],
        'support_address' => ['Jakarta, Indonesia', 'general'],

        'shipping_fee' => ['10000', 'shipping'],
        'service_fee' => ['1000', 'shipping'],
        'free_shipping_threshold' => ['150000', 'shipping'],

        'bank_bca_number' => ['8271928374', 'payment'],
        'bank_bca_holder' => ["D'Cemilinyuk Official", 'payment'],
        'bank_mandiri_number' => ['1370019283741', 'payment'],
        'bank_mandiri_holder' => ["D'Cemilinyuk Official", 'payment'],
        'bank_bri_number' => ['034101002938531', 'payment'],
        'bank_bri_holder' => ["D'Cemilinyuk Official", 'payment'],

        'qris_payload' => ['https://qr.dcemilinyuk.id/pay', 'payment'],
        'cod_enabled' => ['1', 'system'],
        'maintenance_mode' => ['0', 'system'],
    ];

    public function run(): void
    {
        foreach (self::DEFAULTS as $key => [$value, $group]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        Setting::flushCache();
    }
}
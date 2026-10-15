<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingController extends Controller
{
    /**
     * Kunci setting yang boleh dikelola dari panel admin.
 */
public const ALLOWED_KEYS = [
        'brand_name',
        'brand_tagline',
        'wa_number',
        'support_email',
        'support_address',
        'shipping_fee',
        'service_fee',
        'free_shipping_threshold',
        'bank_bca_number',
        'bank_bca_holder',
        'bank_mandiri_number',
        'bank_mandiri_holder',
        'bank_bri_number',
        'bank_bri_holder',
        'qris_payload',
        'cod_enabled',
        'maintenance_mode',
    ];

    /** Kunci bertipe toggle: harus disimpan sebagai '1' / '0', bukan null. */
    public const TOGGLE_KEYS = [
        'cod_enabled',
        'maintenance_mode',
    ];

    public function index()
    {
        $settings = collect(self::ALLOWED_KEYS)
            ->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => ['nullable', 'string', 'max:100'],
            'brand_tagline' => ['nullable', 'string', 'max:200'],
            'wa_number' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\-\s]+$/'],
            'support_email' => ['nullable', 'email', 'max:150'],
            'support_address' => ['nullable', 'string', 'max:500'],
            'shipping_fee' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'service_fee' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'free_shipping_threshold' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'bank_bca_number' => ['nullable', 'string', 'max:30'],
            'bank_bca_holder' => ['nullable', 'string', 'max:100'],
            'bank_mandiri_number' => ['nullable', 'string', 'max:30'],
            'bank_mandiri_holder' => ['nullable', 'string', 'max:100'],
            'bank_bri_number' => ['nullable', 'string', 'max:30'],
            'bank_bri_holder' => ['nullable', 'string', 'max:100'],
            'qris_payload' => ['nullable', 'string', 'max:500'],
            'cod_enabled' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ], [
            'wa_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, atau tanda plus/minus.',
            'support_email.email' => 'Format email tidak valid.',
        ]);

        foreach (self::ALLOWED_KEYS as $key) {
            // Toggle harus selalu tersimpan sebagai '1' / '0' supaya nilainya tidak
            // dianggap "kosong" dan jatuh ke default.
            if (in_array($key, self::TOGGLE_KEYS, true)) {
                Setting::set($key, $request->boolean($key) ? '1' : '0', self::groupFor($key));

                continue;
            }

            if (array_key_exists($key, $validated)) {
                Setting::set($key, $validated[$key], self::groupFor($key));

                continue;
            }

            // Field teks yang tidak dikirim berarti dikosongkan.
            Setting::set($key, null, self::groupFor($key));
        }

        Cache::forget('settings.all');

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    private static function groupFor(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'bank_') => 'payment',
            str_starts_with($key, 'shipping'), str_starts_with($key, 'service') => 'shipping',
            str_starts_with($key, 'brand'), str_starts_with($key, 'wa_'), str_starts_with($key, 'support') => 'general',
            default => 'system',
        };
    }
}
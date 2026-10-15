<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /** Kunci yang aman dipublikasikan ke publik. */
    private const PUBLIC_KEYS = [
        'brand_name',
        'brand_tagline',
        'wa_number',
        'support_email',
        'support_address',
        'shipping_fee',
        'service_fee',
        'free_shipping_threshold',
        'cod_enabled',
    ];

    public function public(): JsonResponse
    {
        $data = collect(self::PUBLIC_KEYS)
            ->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);

        return response()->json([
            'data' => $data,
            'payment_methods' => [
                'bank_transfer' => ['bca', 'mandiri', 'bri'],
                'qris' => Setting::get('qris_payload') !== null,
                'cod' => (bool) Setting::bool('cod_enabled', true),
            ],
        ]);
    }
}
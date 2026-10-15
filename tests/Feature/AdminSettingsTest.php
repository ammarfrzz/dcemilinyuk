<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_settings_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertViewHas('settings');
    }

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'brand_name' => 'CemilinMakmur',
                'wa_number' => '6289988776655',
                'shipping_fee' => 15000,
                'service_fee' => 2000,
                'bank_bca_number' => '1234567890',
                'bank_bca_holder' => 'CemilinMakmur',
                'cod_enabled' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'key' => 'brand_name',
            'value' => 'CemilinMakmur',
        ]);

        Cache::forget(Setting::CACHE_KEY);
        $this->assertSame('CemilinMakmur', Setting::get('brand_name'));
        $this->assertSame(15000, Setting::int('shipping_fee'));
        $this->assertTrue(Setting::bool('cod_enabled'));
    }

    public function test_unchecked_toggles_are_saved_as_disabled(): void
    {
        Setting::set('cod_enabled', '1', 'system');

        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), ['brand_name' => 'Toko']); // cod_enabled tidak dikirim

        $this->assertDatabaseHas('settings', ['key' => 'cod_enabled', 'value' => '0']);

        Cache::forget(Setting::CACHE_KEY);
        $this->assertFalse(Setting::bool('cod_enabled'));
    }

    public function test_settings_validate_wa_number_format(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), ['wa_number' => 'bukan-nomor!!'])
            ->assertSessionHasErrors('wa_number');
    }

    public function test_guest_cannot_update_settings(): void
    {
        $this->put(route('admin.settings.update'), ['brand_name' => 'Hack'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('settings', ['key' => 'brand_name', 'value' => 'Hack']);
    }
}
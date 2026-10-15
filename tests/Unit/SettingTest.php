<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(Setting::CACHE_KEY);
    }

    protected function tearDown(): void
    {
        Cache::forget(Setting::CACHE_KEY);
        parent::tearDown();
    }

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('fallback', Setting::get('tidak_ada', 'fallback'));
        $this->assertNull(Setting::get('tidak_ada'));
    }

    public function test_set_creates_then_updates(): void
    {
        Setting::set('brand_name', 'Awal', 'general');
        $this->assertSame('Awal', Setting::get('brand_name'));

        Setting::set('brand_name', 'Baru', 'general');
        $this->assertSame('Baru', Setting::get('brand_name'));
        $this->assertSame(1, Setting::where('key', 'brand_name')->count());
    }

    public function test_int_and_bool_helpers(): void
    {
        Setting::set('shipping_fee', '12000', 'shipping');
        Setting::set('cod_enabled', '1', 'system');

        $this->assertSame(12000, Setting::int('shipping_fee'));
        $this->assertTrue(Setting::bool('cod_enabled'));
        $this->assertSame(500, Setting::int('tidak_ada', 500));
        $this->assertFalse(Setting::bool('tidak_ada'));
    }

    public function test_group_returns_settings_by_group(): void
    {
        Setting::set('brand_name', 'Toko', 'general');
        Setting::set('shipping_fee', '1000', 'shipping');

        $this->assertCount(1, Setting::group('general'));
        $this->assertCount(1, Setting::group('shipping'));
    }

    public function test_cache_is_invalidated_on_set(): void
    {
        Setting::set('brand_name', 'Satu', 'general');
        $this->assertSame('Satu', Setting::get('brand_name'));

        // bypass cache dengan update langsung
        Setting::where('key', 'brand_name')->update(['value' => 'Dua']);

        $this->assertSame('Satu', Setting::get('brand_name'), 'Nilai harus diambil dari cache');

        Setting::flushCache();
        $this->assertSame('Dua', Setting::get('brand_name'));
    }
}
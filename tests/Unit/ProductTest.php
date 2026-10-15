<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_formatted_price_uses_indonesian_format(): void
    {
        $product = new Product(['price' => 25000]);

        $this->assertSame('Rp 25.000', $product->formatted_price);
    }

    public function test_formatted_price_handles_millions(): void
    {
        $product = new Product(['price' => 1250000]);

        $this->assertSame('Rp 1.250.000', $product->formatted_price);
    }

    public function test_sku_is_zero_padded(): void
    {
        $product = new Product();
        $product->id = 7;

        $this->assertSame('DC-007', $product->sku);
    }

    public function test_badge_labels(): void
    {
        $this->assertSame('Best Seller', (new Product(['badge' => 'best_seller']))->badge_label);
        $this->assertSame('Baru', (new Product(['badge' => 'baru']))->badge_label);
        $this->assertSame('Pedas', (new Product(['badge' => 'pedas']))->badge_label);
        $this->assertNull((new Product(['badge' => null]))->badge_label);
    }

    public function test_badge_css_classes(): void
    {
        $this->assertSame('badge--bestseller', (new Product(['badge' => 'best_seller']))->badge_class);
        $this->assertSame('badge--new', (new Product(['badge' => 'baru']))->badge_class);
        $this->assertSame('badge--pedas', (new Product(['badge' => 'pedas']))->badge_class);
        $this->assertSame('badge--popular', (new Product(['badge' => null]))->badge_class);
    }

    public function test_display_category_falls_back_to_constants(): void
    {
        $product = new Product(['category' => 'MINUMAN']);

        $this->assertSame('Minuman', $product->display_category);
    }

    public function test_stock_status_labels(): void
    {
        $this->assertSame('Aman', (new Product(['stock' => 100, 'min_stock' => 10]))->stock_status_label);
        $this->assertSame('Perlu Restock', (new Product(['stock' => 8, 'min_stock' => 10]))->stock_status_label);
        $this->assertSame('Kritis', (new Product(['stock' => 0, 'min_stock' => 10]))->stock_status_label);
    }

    public function test_stock_status_classes(): void
    {
        $this->assertSame('badge-success', (new Product(['stock' => 100, 'min_stock' => 10]))->stock_status_class);
        $this->assertSame('badge-warning', (new Product(['stock' => 8, 'min_stock' => 10]))->stock_status_class);
        $this->assertSame('badge-danger', (new Product(['stock' => 0, 'min_stock' => 10]))->stock_status_class);
    }

    public function test_is_low_stock(): void
    {
        $this->assertTrue((new Product(['stock' => 5, 'min_stock' => 10]))->is_low_stock);
        $this->assertFalse((new Product(['stock' => 50, 'min_stock' => 10]))->is_low_stock);
    }

    public function test_status_label_reflects_availability(): void
    {
        $this->assertSame('Tersedia', (new Product(['available' => true, 'stock' => 50, 'min_stock' => 10]))->status_label);
        $this->assertSame('Stok Menipis', (new Product(['available' => true, 'stock' => 2, 'min_stock' => 10]))->status_label);
        $this->assertSame('Habis', (new Product(['available' => false, 'stock' => 50, 'min_stock' => 10]))->status_label);
        $this->assertSame('Habis', (new Product(['available' => true, 'stock' => 0, 'min_stock' => 10]))->status_label);
    }

    public function test_whatsapp_url_uses_manual_link_when_set(): void
    {
        $product = new Product([
            'name' => 'Risol Mayo',
            'price' => 3000,
            'whatsapp_order_link' => 'https://wa.me/628111?text=manual',
        ]);

        $this->assertSame('https://wa.me/628111?text=manual', $product->whatsapp_order_url);
    }

    public function test_whatsapp_url_is_generated_with_product_details(): void
    {
        config(['dcemilinyuk.wa_number' => '628999']);

        $product = new Product(['name' => 'Risol Mayo', 'price' => 3000, 'whatsapp_order_link' => null]);

        $url = $product->whatsapp_order_url;

        $this->assertStringStartsWith('https://wa.me/628999?text=', $url);
        $this->assertStringContainsString(rawurlencode('Risol Mayo'), $url);
        $this->assertStringContainsString(rawurlencode('Rp 3.000'), $url);
    }

    public function test_best_sellers_scope_respects_limit_and_availability(): void
    {
        Product::factory()->bestSeller()->create(['name' => 'Terlaris']);
        Product::factory()->create(['name' => 'Biasa', 'sold' => 10]);
        Product::factory()->bestSeller()->unavailable()->create(['name' => 'Terlaris Habis', 'sold' => 5000]);

        $results = Product::bestSellers(3);

        $this->assertCount(2, $results);
        $this->assertEquals('Terlaris', $results->first()->name);
    }

    public function test_in_stock_scope(): void
    {
        Product::factory()->create(['name' => 'Ada']);
        Product::factory()->create(['name' => 'Nol', 'stock' => 0]);
        Product::factory()->unavailable()->create(['name' => 'Off', 'stock' => 10]);

        $names = Product::inStock()->pluck('name')->all();

        $this->assertContains('Ada', $names);
        $this->assertNotContains('Nol', $names);
        $this->assertNotContains('Off', $names);
    }

    public function test_low_stock_scope(): void
    {
        Product::factory()->create(['name' => 'Aman', 'stock' => 100, 'min_stock' => 10]);
        Product::factory()->lowStock()->create(['name' => 'Menipis', 'stock' => 3, 'min_stock' => 10]);

        $names = Product::lowStock()->pluck('name')->all();

        $this->assertSame(['Menipis'], $names);
    }

    public function test_category_scope_and_search_scope(): void
    {
        Product::factory()->create(['name' => 'Es Teh Tarik', 'category' => 'MINUMAN']);
        Product::factory()->create(['name' => 'Kopi Susu', 'category' => 'MINUMAN']);
        Product::factory()->create(['name' => 'Risol Mayo', 'category' => 'CEMILAN']);

        $this->assertSame(2, Product::category('MINUMAN')->count());
        $this->assertCount(1, Product::search('Teh')->get());
        $this->assertCount(3, Product::category(null)->get());
        $this->assertCount(3, Product::search(null)->get());
    }

    public function test_casts_price_and_flags_to_integers_and_booleans(): void
    {
        $product = Product::factory()->create(['price' => '15000', 'available' => 1, 'stock' => '20']);

        $this->assertIsInt($product->price);
        $this->assertIsBool($product->available);
        $this->assertIsInt($product->stock);
    }

    public function test_image_url_falls_back_to_category_image(): void
    {
        $product = Product::factory()->create([
            'name' => 'Produk Tanpa Nama Dikenal',
            'category' => 'KUE',
            'image_path' => null,
        ]);

        $this->assertStringContainsString('donat-kentang', $product->image_url);
    }

    public function test_image_url_prefers_external_url(): void
    {
        $product = Product::factory()->create([
            'image_path' => 'https://cdn.example.com/produk.jpg',
        ]);

        $this->assertSame('https://cdn.example.com/produk.jpg', $product->image_url);
    }
}
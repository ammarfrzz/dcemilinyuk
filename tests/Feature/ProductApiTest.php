<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_unavailable_products_are_hidden(): void
    {
        Product::factory()->create(['name' => 'Tersedia']);
        Product::factory()->unavailable()->create(['name' => 'Habis']);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Tersedia');
    }

    public function test_can_filter_products_by_category(): void
    {
        Product::factory()->create(['category' => 'CEMILAN']);
        Product::factory()->create(['category' => 'MINUMAN']);

        $response = $this->getJson('/api/products?category=CEMILAN');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Risol Mayo Spesial']);
        Product::factory()->create(['name' => 'Kopi Susu Gula Aren']);

        $response = $this->getJson('/api/products?search=Risol');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Risol Mayo Spesial');
    }

    public function test_can_sort_products_by_price(): void
    {
        Product::factory()->create(['name' => 'Murah', 'price' => 5000]);
        Product::factory()->create(['name' => 'Mahal', 'price' => 50000]);

        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Murah')
            ->assertJsonPath('data.1.name', 'Mahal');
    }

    public function test_can_show_single_product(): void
    {
        $product = Product::factory()->create(['name' => 'Donat Kentang', 'price' => 5000]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', 'Donat Kentang')
            ->assertJsonPath('data.formatted_price', 'Rp 5.000')
            ->assertJsonStructure(['data' => ['id', 'sku', 'name', 'price', 'image_url']]);
    }

    public function test_show_returns_404_for_unknown_product(): void
    {
        $this->getJson('/api/products/9999')->assertNotFound();
    }

    public function test_per_page_is_validated(): void
    {
        $this->getJson('/api/products?per_page=999')->assertJsonValidationErrors('per_page');
    }
}
<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_relationship_uses_code(): void
    {
        $category = Category::factory()->code('KUE')->create();
        Product::factory()->count(2)->create(['category' => 'KUE']);
        Product::factory()->create(['category' => 'FROZEN']);

        $this->assertSame(2, $category->products()->count());
    }

    public function test_ordered_scope_returns_active_categories_first(): void
    {
        Category::factory()->code('KODE_A')->create(['sort_order' => 1]);
        Category::factory()->code('KODE_B')->create(['sort_order' => 2]);
        Category::factory()->inactive()->code('KODE_C')->create(['sort_order' => 0]);

        $codes = Category::ordered()->pluck('code')->all();

        $this->assertSame(['KODE_A', 'KODE_B'], $codes);
    }

    public function test_display_category_prefers_loaded_relation(): void
    {
        Category::factory()->code('MINUMAN')->create(['name' => 'Minuman Segar']);

        $product = Product::factory()->create(['category' => 'MINUMAN']);
        $product->load('categoryInfo');

        $this->assertInstanceOf(Category::class, $product->categoryInfo);
        $this->assertSame('Minuman Segar', $product->display_category);
    }

    public function test_casts(): void
    {
        $category = Category::factory()->create(['is_active' => 1, 'sort_order' => '3']);

        $this->assertIsBool($category->is_active);
        $this->assertSame(3, $category->sort_order);
    }
}
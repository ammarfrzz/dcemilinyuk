<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStockTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_stock_page_shows_real_metrics(): void
    {
        Product::factory()->create(['stock' => 100, 'min_stock' => 10]); // aman
        Product::factory()->create(['stock' => 5, 'min_stock' => 10]);  // perlu restock
        Product::factory()->create(['stock' => 0, 'min_stock' => 10]);  // kritis

        $response = $this->actingAs($this->admin())->get(route('admin.stock.index'));

        $response->assertOk()
            ->assertViewHas('totalRegistered', 3)
            ->assertViewHas('totalRestockNeeded', 1)
            ->assertViewHas('totalCritical', 1);
    }

    public function test_restock_increases_stock(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'min_stock' => 10]);

        $this->actingAs($this->admin())
            ->post(route('admin.stock.restock', $product->id), ['quantity' => 20])
            ->assertRedirect();

        $this->assertSame(25, $product->fresh()->stock);
    }

    public function test_restock_validates_quantity(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.stock.restock', $product->id), ['quantity' => 0])
            ->assertSessionHasErrors('quantity');

        $this->actingAs($this->admin())
            ->post(route('admin.stock.restock', $product->id), ['quantity' => 'abc'])
            ->assertSessionHasErrors('quantity');
    }

    public function test_restock_can_reactivate_unavailable_product(): void
    {
        $product = Product::factory()->create(['stock' => 0, 'available' => false]);

        $this->actingAs($this->admin())
            ->post(route('admin.stock.restock', $product->id), ['quantity' => 10]);

        $this->assertTrue($product->fresh()->available);
    }

    public function test_manual_stock_update_marks_empty_as_unavailable(): void
    {
        $product = Product::factory()->create(['stock' => 20, 'available' => true]);

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product->id), ['stock' => 0, 'min_stock' => 5]);

        $product->refresh();

        $this->assertSame(0, $product->stock);
        $this->assertFalse($product->available);
    }

    public function test_manual_stock_update_sets_min_stock(): void
    {
        $product = Product::factory()->create(['stock' => 20]);

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product->id), ['stock' => 20, 'min_stock' => 8]);

        $this->assertSame(8, $product->fresh()->min_stock);
    }

    public function test_manual_stock_rejects_negative_value(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product->id), ['stock' => -1])
            ->assertSessionHasErrors('stock');
    }

    public function test_guest_cannot_restock(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $this->post(route('admin.stock.restock', $product->id), ['quantity' => 5])
            ->assertRedirect(route('login'));

        $this->assertSame(1, $product->fresh()->stock);
    }
}
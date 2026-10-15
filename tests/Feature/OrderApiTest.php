<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        $product = Product::factory()->create(['price' => 25000, 'stock' => 50]);

        return array_merge([
            'product_id' => $product->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'items_count' => 2,
        ], $overrides);
    }

    public function test_can_create_order(): void
    {
        $response = $this->postJson('/api/orders', $this->payload());

        // 2 x 25.000 + ongkir 10.000 + layanan 1.000 = 61.000
        $response->assertCreated()
            ->assertJsonPath('data.order_number', fn ($v) => str_starts_with($v, 'ORD-'))
            ->assertJsonPath('data.status', OrderService::STATUS_AWAITING_PAYMENT)
            ->assertJsonPath('data.payment_status', OrderService::PAYMENT_STATUS_PENDING)
            ->assertJsonPath('data.total_amount', 61000)
            ->assertJsonPath('data.formatted_total', 'Rp 61.000');

        $this->assertDatabaseHas('orders', ['customer_email' => 'budi@example.com', 'items_count' => 2]);
    }

    public function test_total_includes_shipping_and_service_fee(): void
    {
        $payload = $this->payload(['items_count' => 3]);

        // 3 x 25.000 + 10.000 + 1.000 = 86.000
        $this->postJson('/api/orders', $payload)->assertCreated()->assertJsonPath('data.total_amount', 86000);
    }

    public function test_rejects_order_when_stock_insufficient(): void
    {
        $payload = $this->payload();
        Product::find($payload['product_id'])->update(['stock' => 3]);

        $response = $this->postJson('/api/orders', array_merge($payload, ['items_count' => 10]));

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_rejects_order_for_unavailable_product(): void
    {
        $payload = $this->payload();
        Product::find($payload['product_id'])->update(['available' => false]);

        $this->postJson('/api/orders', $payload)->assertStatus(422);
    }

    public function test_validates_required_fields(): void
    {
        $this->postJson('/api/orders', [])
            ->assertJsonValidationErrors([
                'product_id',
                'customer_name',
                'customer_phone',
                'shipping_address',
                'items_count',
            ]);
    }

    public function test_order_number_is_unique(): void
    {
        $this->postJson('/api/orders', $this->payload())->assertCreated();
        $this->postJson('/api/orders', $this->payload())->assertCreated();

        $numbers = Order::pluck('order_number');

        $this->assertCount(2, $numbers);
        $this->assertCount(2, $numbers->unique());
    }

    public function test_can_track_order_with_email(): void
    {
        $created = $this->postJson('/api/orders', $this->payload())->json('data.order_number');

        $this->getJson("/api/orders/{$created}?email=budi@example.com")
            ->assertOk()
            ->assertJsonPath('data.order_number', $created);
    }

    public function test_tracking_order_requires_verification_field(): void
    {
        $created = $this->postJson('/api/orders', $this->payload())->json('data.order_number');

        $this->getJson("/api/orders/{$created}")->assertStatus(422);
    }

    public function test_tracking_order_rejects_wrong_email(): void
    {
        $created = $this->postJson('/api/orders', $this->payload())->json('data.order_number');

        $this->getJson("/api/orders/{$created}?email=pencuri@example.com")->assertNotFound();
    }
}
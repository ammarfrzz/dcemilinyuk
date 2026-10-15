<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function makeOrder(array $attrs = []): Order
    {
        $product = Product::factory()->create();

        return Order::create(array_merge([
            'order_number' => 'ORD-' . uniqid(),
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'items_count' => 1,
            'shipping_fee' => 10000,
            'service_fee' => 1000,
            'total_amount' => $product->price + 11000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'order_date' => now(),
        ], $attrs));
    }

    public function test_admin_can_view_order_list(): void
    {
        $this->makeOrder();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertViewHas('totalOrdersCount', 1);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.update-status', $order->id), ['status' => Order::STATUS_SHIPPED])
            ->assertRedirect();

        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);
    }

    public function test_update_status_rejects_invalid_status(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.update-status', $order->id), ['status' => 'Ngawur'])
            ->assertSessionHasErrors('status');

        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->fresh()->status);
    }

    public function test_order_list_can_be_filtered_by_status(): void
    {
        $this->makeOrder(['status' => Order::STATUS_COMPLETED]);
        $this->makeOrder(['status' => Order::STATUS_PROCESSING]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['status' => Order::STATUS_COMPLETED]));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('orders')->items());
    }

    public function test_order_list_can_be_searched(): void
    {
        $this->makeOrder(['customer_name' => 'Siti Rahayu']);
        $this->makeOrder(['customer_name' => 'Andi Pratama']);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['search' => 'Siti']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('orders')->items());
    }

    public function test_guest_cannot_update_order_status(): void
    {
        $order = $this->makeOrder();

        $this->post(route('admin.orders.update-status', $order->id), ['status' => Order::STATUS_COMPLETED])
            ->assertRedirect(route('login'));

        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->fresh()->status);
    }
}
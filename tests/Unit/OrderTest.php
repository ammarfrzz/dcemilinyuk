<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $attrs = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'ORD-' . uniqid(),
            'customer_name' => 'Budi',
            'customer_phone' => '08123',
            'shipping_address' => 'Jl. Test',
            'product_name' => 'Risol Mayo',
            'unit_price' => 10000,
            'items_count' => 3,
            'shipping_fee' => 10000,
            'service_fee' => 1000,
            'total_amount' => 41000,
        ], $attrs));
    }

    public function test_subtotal_is_unit_price_times_count(): void
    {
        $order = $this->makeOrder();

        $this->assertSame(30000, $order->subtotal);
        $this->assertSame('Rp 30.000', $order->formatted_subtotal);
    }

    public function test_formatted_amounts(): void
    {
        $order = $this->makeOrder();

        $this->assertSame('Rp 41.000', $order->formatted_total);
        $this->assertSame('Rp 10.000', $order->formatted_unit_price);
        $this->assertSame('Rp 10.000', $order->formatted_shipping_fee);
        $this->assertSame('Rp 1.000', $order->formatted_service_fee);
    }

    public function test_free_shipping_is_displayed_as_gratis(): void
    {
        $order = $this->makeOrder(['shipping_fee' => 0]);

        $this->assertSame('Gratis', $order->formatted_shipping_fee);
    }

    public function test_payment_method_labels(): void
    {
        $this->assertSame('Transfer Bank BCA', $this->makeOrder(['payment_method' => 'bca'])->payment_method_label);
        $this->assertSame('Transfer Bank Mandiri', $this->makeOrder(['payment_method' => 'mandiri'])->payment_method_label);
        $this->assertSame('Transfer Bank BRI', $this->makeOrder(['payment_method' => 'bri'])->payment_method_label);
        $this->assertStringContainsString('QRIS', $this->makeOrder(['payment_method' => 'qris'])->payment_method_label);
        $this->assertStringContainsString('COD', $this->makeOrder(['payment_method' => 'cod'])->payment_method_label);
        $this->assertSame('Belum Dipilih', $this->makeOrder(['payment_method' => null])->payment_method_label);
    }

    public function test_status_badge_classes(): void
    {
        $this->assertSame('badge-success', $this->makeOrder(['status' => Order::STATUS_COMPLETED])->status_badge_class);
        $this->assertSame('badge-warning', $this->makeOrder(['status' => Order::STATUS_PROCESSING])->status_badge_class);
        $this->assertSame('badge-info', $this->makeOrder(['status' => Order::STATUS_SHIPPED])->status_badge_class);
        $this->assertSame('badge-danger', $this->makeOrder(['status' => Order::STATUS_CANCELLED])->status_badge_class);
        $this->assertSame('badge-secondary', $this->makeOrder(['status' => Order::STATUS_AWAITING_PAYMENT])->status_badge_class);
    }

    public function test_is_cancelled_accessor(): void
    {
        $this->assertTrue($this->makeOrder(['status' => Order::STATUS_CANCELLED])->is_cancelled);
        $this->assertFalse($this->makeOrder(['status' => Order::STATUS_PROCESSING])->is_cancelled);
    }

    public function test_amounts_are_cast_to_integers(): void
    {
        $order = $this->makeOrder();

        $this->assertIsInt($order->unit_price);
        $this->assertIsInt($order->items_count);
        $this->assertIsInt($order->total_amount);
    }

    public function test_active_scope_excludes_cancelled(): void
    {
        $this->makeOrder(['status' => Order::STATUS_PROCESSING]);
        $this->makeOrder(['status' => Order::STATUS_CANCELLED]);

        $this->assertSame(1, Order::active()->count());
    }

    public function test_paid_scope(): void
    {
        $this->makeOrder(['payment_status' => Order::PAYMENT_STATUS_PAID]);
        $this->makeOrder(['payment_status' => Order::PAYMENT_STATUS_PENDING]);

        $this->assertSame(1, Order::paid()->count());
    }
}
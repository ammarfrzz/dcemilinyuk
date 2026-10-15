<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        return Product::factory()->create([
            'name' => 'Risol Mayo',
            'price' => 3000,
            'stock' => 100,
            'sold' => 0,
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'product_id' => $product->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'items_count' => 2,
        ], $overrides);
    }

    public function test_store_creates_order_and_redirects_to_payment(): void
    {
        $product = $this->product();

        $this->post(route('checkout.store'), $this->payload($product))
            ->assertRedirect();

        $order = \App\Models\Order::firstOrFail();

        $this->assertSame(OrderService::STATUS_AWAITING_PAYMENT, $order->status);
        $this->assertSame('Risol Mayo', $order->product_name);
        // 2 x 3.000 + 10.000 + 1.000 = 17.000
        $this->assertSame(17000, $order->total_amount);
    }

    public function test_store_validates_input(): void
    {
        $this->post(route('checkout.store'), [])
            ->assertSessionHasErrors([
                'product_id',
                'customer_name',
                'customer_phone',
                'shipping_address',
                'items_count',
            ]);
    }

    public function test_store_rejects_items_count_below_one(): void
    {
        $product = $this->product();

        $this->post(route('checkout.store'), $this->payload($product, ['items_count' => 0]))
            ->assertSessionHasErrors('items_count');
    }

    public function test_payment_page_shows_order(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));

        $order = \App\Models\Order::firstOrFail();

        $this->get(route('checkout.payment', $order->order_number))
            ->assertOk()
            ->assertViewHas('order');
    }

    public function test_payment_page_404_for_unknown_order(): void
    {
        $this->get(route('checkout.payment', 'ORD-NOT-EXIST'))->assertNotFound();
    }

    public function test_only_configured_banks_are_shown(): void
    {
        Setting::set('bank_bca_number', '111222333');
        Setting::set('bank_bri_number', null);
        Setting::flushCache();

        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $response = $this->get(route('checkout.payment', $order->order_number));

        $banks = $response->viewData('bankAccounts');

        $this->assertArrayHasKey('bca', $banks);
        $this->assertArrayNotHasKey('bri', $banks);
    }

    public function test_update_changes_quantity_and_recalculates_total(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->put(route('checkout.update', $order->order_number), ['items_count' => 5])
            ->assertRedirect(route('checkout.payment', $order->order_number));

        $order->refresh();

        $this->assertSame(5, $order->items_count);
        // 5 x 3.000 + 10.000 + 1.000 = 26.000
        $this->assertSame(26000, $order->total_amount);
    }

    public function test_update_returns_json_for_ajax(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->putJson(route('checkout.update', $order->order_number), ['items_count' => 4])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('items_count', 4)
            ->assertJsonPath('total_amount', 'Rp 23.000');
    }

    public function test_update_rejects_quantity_beyond_stock(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->putJson(route('checkout.update', $order->order_number), ['items_count' => 999])
            ->assertStatus(422);

        $this->assertSame(2, $order->fresh()->items_count);
    }

    public function test_confirm_with_bank_transfer_sets_pending_verification(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'bca'])
            ->assertRedirect(route('checkout.success', $order->order_number));

        $order->refresh();

        $this->assertSame('bca', $order->payment_method);
        $this->assertSame(OrderService::PAYMENT_STATUS_PENDING_VERIFICATION, $order->payment_status);
        $this->assertSame(OrderService::STATUS_PROCESSING, $order->status);
    }

    public function test_confirm_with_cod_sets_cod_pending(): void
    {
        Setting::set('cod_enabled', '1', 'system');
        Setting::flushCache();

        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'cod']);

        $this->assertSame(OrderService::PAYMENT_STATUS_COD_PENDING, $order->fresh()->payment_status);
    }

    public function test_cod_is_rejected_when_disabled(): void
    {
        Setting::set('cod_enabled', '0', 'system');
        Setting::flushCache();

        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'cod'])
            ->assertSessionHasErrors('payment_method');

        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_confirm_validates_payment_method(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'bitcoin'])
            ->assertSessionHasErrors('payment_method');
    }

    public function test_confirm_reduces_stock_and_increments_sold(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'bca']);

        $product->refresh();

        $this->assertSame(98, $product->stock); // 100 - 2
        $this->assertSame(2, $product->sold);
    }

    public function test_confirming_twice_does_not_double_reduce_stock(): void
    {
        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'bca']);
        $this->post(route('checkout.confirm', $order->order_number), ['payment_method' => 'bca']);

        $product->refresh();

        $this->assertSame(98, $product->stock);
        $this->assertSame(2, $product->sold);
    }

    public function test_success_page_builds_whatsapp_link(): void
    {
        Setting::set('wa_number', '6289988776655', 'general');
        Setting::flushCache();

        $product = $this->product();
        $this->post(route('checkout.store'), $this->payload($product));
        $order = \App\Models\Order::firstOrFail();

        $response = $this->get(route('checkout.success', $order->order_number));

        $response->assertOk()->assertViewHas('whatsappUrl');

        $url = $response->viewData('whatsappUrl');
        $this->assertStringStartsWith('https://wa.me/6289988776655?text=', $url);
    }
}
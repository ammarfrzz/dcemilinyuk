<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public const STATUS_AWAITING_PAYMENT = 'Menunggu Pembayaran';
    public const STATUS_PROCESSING = 'Diproses';
    public const STATUS_SHIPPED = 'Dikirim';
    public const STATUS_COMPLETED = 'Selesai';
    public const STATUS_CANCELLED = 'Dibatalkan';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const PAYMENT_STATUS_COD_PENDING = 'cod_pending';
    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_METHODS = ['bca', 'mandiri', 'bri', 'qris', 'cod'];

    /**
     * Buat pesanan baru lengkap dengan perhitungan ongkir & total.
     *
     * @param  array<string, mixed>  $data  Data tervalidasi (product_id, items_count, data pemesan, dll)
     */
    public function create(array $data): Order
    {
        $product = Product::find($data['product_id'] ?? null);

        if (! $product) {
            throw new InvalidArgumentException('Produk tidak ditemukan.');
        }

        if (! $product->available || $product->stock <= 0) {
            throw new InvalidArgumentException("Produk '{$product->name}' sedang tidak tersedia.");
        }

        $itemsCount = (int) ($data['items_count'] ?? 1);

        if ($itemsCount < 1) {
            throw new InvalidArgumentException('Jumlah pesanan minimal 1 item.');
        }

        if ($itemsCount > $product->stock) {
            throw new InvalidArgumentException("Stok '{$product->name}' tidak mencukupi (tersisa {$product->stock}).");
        }

        $subtotal = $product->price * $itemsCount;
        $shippingFee = $this->calculateShippingFee($subtotal);
        $serviceFee = Setting::int('service_fee', 1000);
        $totalAmount = $subtotal + $shippingFee + $serviceFee;

        return Order::create([
            'order_number' => $this->generateOrderNumber(),
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'] ?? null,
            'shipping_address' => $data['shipping_address'],
            'order_notes' => $data['order_notes'] ?? null,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'items_count' => $itemsCount,
            'shipping_fee' => $shippingFee,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'status' => self::STATUS_AWAITING_PAYMENT,
            'payment_status' => self::PAYMENT_STATUS_PENDING,
            'order_date' => now(),
        ]);
    }

    /**
     * Hitung ongkir. Gratis ongkir bila total belanja melewati ambang batas.
     */
    public function calculateShippingFee(int $subtotal): int
    {
        $freeThreshold = Setting::int('free_shipping_threshold', 0);

        if ($freeThreshold > 0 && $subtotal >= $freeThreshold) {
            return 0;
        }

        return Setting::int('shipping_fee', 10000);
    }

    /**
     * Kurangi stok produk setelah pesanan dikonfirmasi (idempoten per status).
     */
    public function reduceStock(Order $order): void
    {
        $product = $order->product;

        if (! $product) {
            return;
        }

        $remaining = max(0, $product->stock - $order->items_count);

        $product->update([
            'stock' => $remaining,
            'available' => $remaining > 0 ? $product->available : false,
        ]);

        $product->increment('sold', $order->items_count);
    }

    /**
     * Nomor pesanan unik: ORD-YYYYMMDD-XXXX
     */
    public function generateOrderNumber(): string
    {
        do {
            $candidate = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Order::where('order_number', $candidate)->exists());

        return $candidate;
    }
}
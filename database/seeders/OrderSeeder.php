<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command?->warn('Belum ada produk. Jalankan ProductSeeder terlebih dahulu.');

            return;
        }

        $customers = [
            ['Eddria Wijaya', 'eddria@gmail.com', '081298765432'],
            ['Ammaliya Putri', 'ammaliya@gmail.com', '081298765431'],
            ['Budi Santoso', 'budi.santoso@yahoo.com', '081298765430'],
            ['Nabila Rahma', 'nabila.r@outlook.com', '081298765429'],
            ['Rizky Pratama', 'rizky.p@gmail.com', '081298765428'],
            ['Dewi Lestari', 'dewi.lestari@gmail.com', '081298765427'],
        ];

        $statuses = Order::STATUSES;
        $paymentMethods = ['bca', 'mandiri', 'bri', 'qris', 'cod'];

        foreach (range(1, 25) as $i) {
            $product = $products->random();
            [$name, $email, $phone] = $customers[array_rand($customers)];

            $itemsCount = random_int(1, 5);
            $subtotal = $product->price * $itemsCount;
            $shippingFee = $subtotal >= 150000 ? 0 : 10000;
            $serviceFee = 1000;
            $status = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            $orderDate = now()->subDays(random_int(0, 29))->subHours(random_int(0, 23));

            Order::updateOrCreate(
                ['order_number' => 'ORD-' . $orderDate->format('Ymd') . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT)],
                [
                    'customer_name' => $name,
                    'customer_phone' => $phone,
                    'customer_email' => $email,
                    'shipping_address' => 'Jl. Contoh No. ' . random_int(1, 99) . ', Jakarta',
                    'order_notes' => null,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'items_count' => $itemsCount,
                    'shipping_fee' => $shippingFee,
                    'service_fee' => $serviceFee,
                    'total_amount' => $subtotal + $shippingFee + $serviceFee,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $status === Order::STATUS_AWAITING_PAYMENT
                        ? Order::PAYMENT_STATUS_PENDING
                        : ($paymentMethod === 'cod'
                            ? Order::PAYMENT_STATUS_COD_PENDING
                            : Order::PAYMENT_STATUS_PAID),
                    'status' => $status,
                    'order_date' => $orderDate,
                ]
            );
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'order_notes' => ['nullable', 'string', 'max:500'],
            'items_count' => ['required', 'integer', 'min:1', 'max:99'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'items_count.required' => 'Jumlah pesanan wajib diisi.',
            'items_count.min' => 'Jumlah pesanan minimal 1 item.',
            'items_count.max' => 'Jumlah pesanan maksimal 99 item.',
            'customer_email.email' => 'Format email tidak valid.',
        ]);

        try {
            $order = $this->orderService->create($validated);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'data' => $this->transform($order),
        ], 201);
    }

    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:25'],
        ]);

        $email = $validated['email'] ?? null;
        $phone = $validated['phone'] ?? null;

        if (! $email && ! $phone) {
            return response()->json([
                'message' => 'Sertakan email atau nomor HP untuk memverifikasi pesanan.',
            ], 422);
        }

        $order = Order::with('product')
            ->where('order_number', $orderNumber)
            ->when($email, fn ($q) => $q->where('customer_email', $email))
            ->when($phone, fn ($q) => $q->where('customer_phone', $phone))
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data pesanan.',
            'data' => $this->transform($order),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Order $order): array
    {
        return [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_method' => $order->payment_method_label,
            'payment_status' => $order->payment_status,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'shipping_address' => $order->shipping_address,
            'order_notes' => $order->order_notes,
            'product' => [
                'id' => $order->product_id,
                'name' => $order->product_name,
                'image_url' => $order->product?->image_url,
            ],
            'items_count' => $order->items_count,
            'unit_price' => $order->unit_price,
            'shipping_fee' => $order->shipping_fee,
            'service_fee' => $order->service_fee,
            'total_amount' => $order->total_amount,
            'formatted_total' => $order->formatted_total,
            'order_date' => $order->order_date?->toIso8601String(),
        ];
    }
}
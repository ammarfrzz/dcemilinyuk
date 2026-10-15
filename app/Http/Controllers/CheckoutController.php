<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Langkah 1: Simpan data pemesan awal dan arahkan ke Halaman Pembayaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'customer_email' => 'nullable|email|max:150',
            'shipping_address' => 'required|string|max:500',
            'order_notes' => 'nullable|string|max:500',
            'items_count' => 'required|integer|min:1|max:99',
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'items_count.required' => 'Jumlah pesanan wajib diisi.',
            'items_count.min' => 'Jumlah pesanan minimal 1 item.',
            'items_count.max' => 'Jumlah pesanan maksimal 99 item.',
        ]);

        try {
            $order = $this->orderService->create($validated);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.payment', ['order_number' => $order->order_number])
            ->with('success', 'Data pemesanan berhasil disimpan! Silakan pilih metode pembayaran.');
    }

    /**
     * Langkah 2: Tampilkan Halaman Pembayaran & Review Detail Orderan
     */
    public function payment(string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.payment', [
            'order' => $order,
            'bankAccounts' => $this->bankAccounts(),
        ]);
    }

    /**
     * Atur kembali detail orderan (kuantitas, alamat, catatan)
     */
    public function update(Request $request, string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->status === OrderService::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan dan tidak bisa diubah.');
        }

        $validated = $request->validate([
            'items_count' => 'sometimes|required|integer|min:1|max:99',
            'customer_name' => 'sometimes|required|string|max:100',
            'customer_phone' => 'sometimes|required|string|max:25',
            'customer_email' => 'sometimes|nullable|email|max:150',
            'shipping_address' => 'sometimes|required|string|max:500',
            'order_notes' => 'sometimes|nullable|string|max:500',
        ]);

        if (isset($validated['items_count'])) {
            $itemsCount = (int) $validated['items_count'];

            if ($order->product && $itemsCount > $order->product->stock) {
                return $this->respondError($request, "Stok '{$order->product->name}' tidak mencukupi (tersisa {$order->product->stock}).");
            }

            $order->items_count = $itemsCount;
            $order->total_amount = ($order->unit_price * $itemsCount) + $order->shipping_fee + $order->service_fee;
        }

        $order->fill($request->only([
            'customer_name',
            'customer_phone',
            'customer_email',
            'shipping_address',
            'order_notes',
        ]));

        $order->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui!',
                'items_count' => $order->items_count,
                'subtotal' => $order->formatted_subtotal,
                'total_amount' => $order->formatted_total,
                'shipping_fee' => $order->formatted_shipping_fee,
                'service_fee' => $order->formatted_service_fee,
            ]);
        }

        return redirect()->route('checkout.payment', ['order_number' => $order->order_number])
            ->with('success', 'Rincian pesanan berhasil diperbarui!');
    }

    /**
     * Langkah 3: Konfirmasi Metode Pembayaran (Transfer Bank / QRIS / COD)
     */
    public function confirm(Request $request, string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        $availableMethods = array_filter(
            OrderService::PAYMENT_METHODS,
            fn (string $method) => $method !== 'cod' || Setting::bool('cod_enabled', true)
        );

        $validated = $request->validate([
            'payment_method' => ['required', 'in:' . implode(',', $availableMethods)],
        ], [
            'payment_method.required' => 'Silakan pilih salah satu metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid atau sedang dinonaktifkan.',
        ]);

        $isCod = $validated['payment_method'] === 'cod';

        // Kurangi stok hanya pada konfirmasi pertama supaya tidak double decrement
        // kalau pembeli me-refresh atau submit ulang.
        $isFirstConfirmation = $order->payment_status === OrderService::PAYMENT_STATUS_PENDING;

        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => $isCod ? OrderService::PAYMENT_STATUS_COD_PENDING : OrderService::PAYMENT_STATUS_PENDING_VERIFICATION,
            'status' => OrderService::STATUS_PROCESSING,
        ]);

        if ($isFirstConfirmation) {
            $this->orderService->reduceStock($order);
        }

        return redirect()->route('checkout.success', ['order_number' => $order->order_number])
            ->with('success', 'Pesanan dan metode pembayaran berhasil dikonfirmasi!');
    }

    /**
     * Langkah 4: Halaman Pesanan Berhasil / Bukti Transaksi
     */
    public function success(string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        $brand = Setting::get('brand_name', config('dcemilinyuk.brand', "D'Cemilinyuk"));
        $waNumber = Setting::get('wa_number', config('dcemilinyuk.wa_number', '6281234567890'));

        // Siapkan pesan template WhatsApp jika customer ingin chat admin
        $waMessage = "Halo Admin {$brand}!\n"
            . "Saya ingin konfirmasi pesanan dengan detail:\n\n"
            . "• No. Pesanan: *{$order->order_number}*\n"
            . "• Nama: {$order->customer_name}\n"
            . "• Produk: {$order->product_name} ({$order->items_count}x)\n"
            . "• Total: {$order->formatted_total}\n"
            . "• Metode Bayar: {$order->payment_method_label}\n"
            . "• Alamat: {$order->shipping_address}\n\n"
            . "Mohon segera diproses ya, terima kasih!";

        $whatsappUrl = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($waMessage);

        return view('checkout.success', compact('order', 'whatsappUrl'));
    }

    /**
     * Daftar rekening resmi, diambil dari tabel settings.
     *
     * @return array<string, array<string, string|bool>>
     */
    private function bankAccounts(): array
    {
        return array_filter([
            'bca' => $this->bankAccount('bca', 'BCA (Bank Central Asia)', 'fa-solid fa-building-columns', 'Otomatis / Manual Check'),
            'mandiri' => $this->bankAccount('mandiri', 'Bank Mandiri', 'fa-solid fa-landmark', 'Bebas Admin via Mandiri'),
            'bri' => $this->bankAccount('bri', 'Bank BRI', 'fa-solid fa-money-bill-transfer', 'Support BRImo & ATM'),
        ], fn ($account) => $account !== null);
    }

    /**
     * @return array<string, string|bool>|null
     */
    private function bankAccount(string $key, string $name, string $icon, string $badge): ?array
    {
        $number = Setting::get("bank_{$key}_number");

        if (empty($number)) {
            return null;
        }

        return [
            'name' => $name,
            'code' => strtoupper($key),
            'account_number' => $number,
            'account_holder' => Setting::get("bank_{$key}_holder", config('dcemilinyuk.brand', "D'Cemilinyuk Official")),
            'icon' => $icon,
            'badge' => $badge,
        ];
    }

    private function respondError(Request $request, string $message)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->withInput()->with('error', $message);
    }
}
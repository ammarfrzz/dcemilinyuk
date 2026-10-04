<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Langkah 1: Simpan data pemesan awal dan arahkan ke Halaman Pembayaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'customer_email' => 'nullable|email|max:100',
            'shipping_address' => 'required|string|max:500',
            'order_notes' => 'nullable|string|max:500',
            'items_count' => 'required|integer|min:1|max:99',
        ], [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'items_count.min' => 'Jumlah pesanan minimal 1 item.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $unitPrice = $product->price;
        $itemsCount = (int) $validated['items_count'];
        $shippingFee = 10000; // Flat Rp 10.000
        $serviceFee = 1000;  // Biaya penanganan
        $totalAmount = ($unitPrice * $itemsCount) + $shippingFee + $serviceFee;

        // Generate Order Number unik misal ORD-20261003-8A2F
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'order_notes' => $validated['order_notes'] ?? null,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $unitPrice,
            'items_count' => $itemsCount,
            'shipping_fee' => $shippingFee,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'status' => 'Menunggu Pembayaran',
            'payment_status' => 'pending',
            'order_date' => now(),
        ]);

        return redirect()->route('checkout.payment', ['order_number' => $order->order_number])
            ->with('success', 'Data pemesanan berhasil disimpan! Silakan pilih metode pembayaran.');
    }

    /**
     * Langkah 2: Tampilkan Halaman Pembayaran & Review Detail Orderan
     */
    public function payment(string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        // Data rekening transfer resmi DcemilinYuk
        $bankAccounts = [
            'bca' => [
                'name' => 'BCA (Bank Central Asia)',
                'code' => 'BCA',
                'account_number' => '8271928374',
                'account_holder' => "D'Cemilinyuk Official",
                'icon' => 'fa-solid fa-building-columns',
                'badge' => 'Otomatis / Manual Check',
            ],
            'mandiri' => [
                'name' => 'Bank Mandiri',
                'code' => 'MANDIRI',
                'account_number' => '1370019283741',
                'account_holder' => "D'Cemilinyuk Official",
                'icon' => 'fa-solid fa-landmark',
                'badge' => 'Bebas Admin via Mandiri',
            ],
            'bri' => [
                'name' => 'Bank BRI',
                'code' => 'BRI',
                'account_number' => '034101002938531',
                'account_holder' => "D'Cemilinyuk Official",
                'icon' => 'fa-solid fa-money-bill-transfer',
                'badge' => 'Support BRImo & ATM',
            ],
        ];

        return view('checkout.payment', compact('order', 'bankAccounts'));
    }

    /**
     * Atur kembali detail orderan (kuantitas, alamat, catatan)
     */
    public function update(Request $request, string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        $validated = $request->validate([
            'items_count' => 'sometimes|required|integer|min:1|max:99',
            'customer_name' => 'sometimes|required|string|max:100',
            'customer_phone' => 'sometimes|required|string|max:25',
            'customer_email' => 'sometimes|nullable|email|max:100',
            'shipping_address' => 'sometimes|required|string|max:500',
            'order_notes' => 'sometimes|nullable|string|max:500',
        ]);

        if (isset($validated['items_count'])) {
            $itemsCount = (int) $validated['items_count'];
            $order->items_count = $itemsCount;
            $order->total_amount = ($order->unit_price * $itemsCount) + $order->shipping_fee + $order->service_fee;
        }

        if (isset($validated['customer_name'])) {
            $order->customer_name = $validated['customer_name'];
        }
        if (isset($validated['customer_phone'])) {
            $order->customer_phone = $validated['customer_phone'];
        }
        if (array_key_exists('customer_email', $validated)) {
            $order->customer_email = $validated['customer_email'];
        }
        if (isset($validated['shipping_address'])) {
            $order->shipping_address = $validated['shipping_address'];
        }
        if (array_key_exists('order_notes', $validated)) {
            $order->order_notes = $validated['order_notes'];
        }

        $order->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui!',
                'items_count' => $order->items_count,
                'subtotal' => 'Rp ' . number_format($order->unit_price * $order->items_count, 0, ',', '.'),
                'total_amount' => $order->formatted_total,
                'shipping_fee' => $order->formatted_shipping_fee,
                'service_fee' => 'Rp ' . number_format($order->service_fee, 0, ',', '.'),
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
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $validated = $request->validate([
            'payment_method' => 'required|in:bca,mandiri,bri,qris,cod',
        ], [
            'payment_method.required' => 'Silakan pilih salah satu metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);

        $order->payment_method = $validated['payment_method'];

        if ($validated['payment_method'] === 'cod') {
            $order->payment_status = 'cod_pending';
            $order->status = 'Diproses';
        } else {
            $order->payment_status = 'pending_verification';
            $order->status = 'Diproses';
        }

        $order->save();

        return redirect()->route('checkout.success', ['order_number' => $order->order_number])
            ->with('success', 'Pesanan dan metode pembayaran berhasil dikonfirmasi!');
    }

    /**
     * Langkah 4: Halaman Pesanan Berhasil / Bukti Transaksi
     */
    public function success(string $orderNumber)
    {
        $order = Order::with('product')->where('order_number', $orderNumber)->firstOrFail();

        // Siapkan pesan template WhatsApp jika customer ingin chat admin
        $waMessage = "Halo Admin " . config('dcemilinyuk.brand') . "!\n" .
                     "Saya ingin konfirmasi pesanan dengan detail:\n\n" .
                     "• No. Pesanan: *" . $order->order_number . "*\n" .
                     "• Nama: " . $order->customer_name . "\n" .
                     "• Produk: " . $order->product_name . " (" . $order->items_count . "x)\n" .
                     "• Total: " . $order->formatted_total . "\n" .
                     "• Metode Bayar: " . $order->payment_method_label . "\n" .
                     "• Alamat: " . $order->shipping_address . "\n\n" .
                     "Mohon segera diproses ya, terima kasih!";

        $whatsappUrl = 'https://wa.me/' . config('dcemilinyuk.wa_number') . '?text=' . rawurlencode($waMessage);

        return view('checkout.success', compact('order', 'whatsappUrl'));
    }
}

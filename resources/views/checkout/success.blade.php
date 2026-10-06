@extends('layouts.app')

@section('content')
<div class="checkout-page checkout-page--success">
    <div class="container checkout-container checkout-container--narrow">

        {{-- Tombol Navigasi Kembali ke Pembayaran / Ubah Pesanan --}}
        <div class="checkout-top-bar">
            <a href="{{ route('checkout.payment', $order->order_number) }}" class="btn-checkout-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Metode Pembayaran</span>
            </a>
        </div>

        {{-- Stepper Progress --}}
        <div class="checkout-stepper">
            <div class="step-item step-item--completed">
                <div class="step-item__circle"><i class="fa-solid fa-check"></i></div>
                <div class="step-item__text">
                    <span class="step-num">Langkah 1</span>
                    <span class="step-label">Data Diri & Alamat</span>
                </div>
            </div>
            <div class="step-line step-line--completed"></div>
            <div class="step-item step-item--completed">
                <div class="step-item__circle"><i class="fa-solid fa-check"></i></div>
                <div class="step-item__text">
                    <span class="step-num">Langkah 2</span>
                    <span class="step-label">Pembayaran</span>
                </div>
            </div>
            <div class="step-line step-line--completed"></div>
            <div class="step-item step-item--active">
                <div class="step-item__circle"><i class="fa-solid fa-flag-checkered"></i></div>
                <div class="step-item__text">
                    <span class="step-num">Langkah 3</span>
                    <span class="step-label">Selesai</span>
                </div>
            </div>
        </div>

        {{-- Kartu Sukses Pesanan --}}
        <div class="order-success-card">
            <div class="order-success-card__badge">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="order-success-card__title">Pesanan Berhasil Dibuat!</h1>
            <p class="order-success-card__desc">
                Terima kasih atas pesananmu di <strong>{{ config('dcemilinyuk.brand') }}</strong>. Pesananmu telah tercatat dalam sistem dan segera kami siapkan.
            </p>

            {{-- Info Pill Header --}}
            <div class="success-highlight-box">
                <div class="highlight-item">
                    <span class="highlight-label">No. Pesanan</span>
                    <strong class="highlight-value text-primary">{{ $order->order_number }}</strong>
                </div>
                <div class="highlight-item">
                    <span class="highlight-label">Metode Pembayaran</span>
                    <strong class="highlight-value">{{ $order->payment_method_label }}</strong>
                </div>
                <div class="highlight-item">
                    <span class="highlight-label">Total Tagihan</span>
                    <strong class="highlight-value text-accent">{{ $order->formatted_total }}</strong>
                </div>
            </div>

            {{-- Instruksi Pembayaran Lanjutan --}}
            <div class="payment-action-card">
                @if (in_array($order->payment_method, ['bca', 'mandiri', 'bri']))
                    <div class="action-card-header">
                        <i class="fa-solid fa-building-columns text-primary"></i>
                        <h4>Langkah Pembayaran Transfer Bank</h4>
                    </div>
                    <p class="action-card-text">
                        Silakan transfer sebesar <strong class="text-accent">{{ $order->formatted_total }}</strong> ke rekening:
                    </p>
                    <div class="bank-pill-display">
                        @if ($order->payment_method === 'bca')
                            <span>Bank BCA: <strong>8271928374</strong> a.n. D'Cemilinyuk Official</span>
                        @elseif ($order->payment_method === 'mandiri')
                            <span>Bank Mandiri: <strong>1370019283741</strong> a.n. D'Cemilinyuk Official</span>
                        @else
                            <span>Bank BRI: <strong>034101002938531</strong> a.n. D'Cemilinyuk Official</span>
                        @endif
                    </div>
                    <p class="action-card-subtext">
                        Setelah melakukan transfer, silakan klik tombol WhatsApp di bawah untuk mengirimkan bukti transfer agar pesananmu langsung diproses.
                    </p>
                @elseif ($order->payment_method === 'qris')
                    <div class="action-card-header">
                        <i class="fa-solid fa-qrcode text-accent"></i>
                        <h4>Konfirmasi Pembayaran QRIS</h4>
                    </div>
                    <p class="action-card-text">
                        Silakan scan kode QRIS di bawah ini dengan total pembayaran <strong class="text-accent">{{ $order->formatted_total }}</strong>, simpan bukti tangkapan layar pembayaranmu dan konfirmasi ke admin kami via WhatsApp.
                    </p>

                    <div class="qris-display-box" style="margin-top: 1.25rem;">
                        <div class="qris-card">
                            <div class="qris-header">
                                <span class="qris-logo-text">QRIS</span>
                                <span class="qris-gpn-text">GPN</span>
                            </div>
                            <div class="qris-image-wrap">
                                {{-- QR Code dinamis via Google Charts API untuk nomor order --}}
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=00020101021226600016ID.CO.DCEMILINYUK.WWW01189360091800000000005204581253033605802ID5912DCEMILINYUK6007JAKARTA62070703A016304{{ $order->order_number }}"
                                     alt="QRIS DcemilinYuk" class="qris-image" loading="lazy">
                            </div>
                            <div class="qris-merchant-info">
                                <strong>{{ config('dcemilinyuk.brand') }} Official</strong>
                                <span>NMID: ID1020268899201</span>
                            </div>
                        </div>
                        <div class="qris-steps">
                            <div class="qris-step-item">
                                <span class="qris-step-num">1</span>
                                <span>Buka aplikasi E-Wallet (GoPay, OVO, Dana) atau Mobile Banking kamu.</span>
                            </div>
                            <div class="qris-step-item">
                                <span class="qris-step-num">2</span>
                                <span>Pilih menu <strong>Scan QR</strong> dan arahkan kamera ke barcode di atas.</span>
                            </div>
                            <div class="qris-step-item">
                                <span class="qris-step-num">3</span>
                                <span>Periksa nominal tagihan sesuai total pesanan, lalu selesaikan pembayaran.</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="action-card-header">
                        <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                        <h4>Pesanan COD (Bayar di Tempat) Diterima</h4>
                    </div>
                    <p class="action-card-text">
                        Pesananmu sedang disiapkan di dapur dan akan segera diantar oleh kurir. Harap siapkan uang pas sebesar <strong class="text-accent">{{ $order->formatted_total }}</strong> saat kurir tiba di lokasimu.
                    </p>
                @endif
            </div>

            {{-- Rincian Lengkap Nota / Invoice --}}
            <div class="receipt-box" id="printableReceipt">
                <div class="receipt-header">
                    <h4>Rincian Nota Transaksi</h4>
                    <span class="badge-status {{ $order->status_badge_class }}">{{ $order->status }}</span>
                </div>

                <div class="receipt-meta">
                    <div>
                        <span class="meta-label">Pemesan:</span>
                        <div class="meta-val"><strong>{{ $order->customer_name }}</strong> ({{ $order->customer_phone }})</div>
                    </div>
                    <div>
                        <span class="meta-label">Alamat Antar:</span>
                        <div class="meta-val">{{ $order->shipping_address }}</div>
                    </div>
                    @if ($order->order_notes)
                        <div>
                            <span class="meta-label">Catatan:</span>
                            <div class="meta-val">"{{ $order->order_notes }}"</div>
                        </div>
                    @endif
                </div>

                <div class="receipt-table-wrap">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th class="col-product">Produk</th>
                                <th class="col-qty text-center">Jumlah</th>
                                <th class="col-price text-right">Harga Satuan</th>
                                <th class="col-subtotal text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-product">
                                    <strong class="receipt-product-name">{{ $order->product_name }}</strong>
                                </td>
                                <td class="col-qty text-center">
                                    {{ $order->items_count }}x
                                </td>
                                <td class="col-price text-right">
                                    {{ $order->formatted_unit_price }}
                                </td>
                                <td class="col-subtotal text-right">
                                    <span class="subtotal-val">Rp {{ number_format($order->unit_price * $order->items_count, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="foot-label text-left">Ongkos Kirim:</td>
                                <td class="col-subtotal foot-val text-right">{{ $order->formatted_shipping_fee }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="foot-label text-left">Biaya Layanan:</td>
                                <td class="col-subtotal foot-val text-right">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" class="foot-label text-left"><strong>Total Pembayaran:</strong></td>
                                <td class="col-subtotal foot-val text-right total-amount">{{ $order->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="success-actions">
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-wa-confirm">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Kirim Konfirmasi ke WhatsApp</span>
                </a>

                <button type="button" class="btn-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Cetak / Simpan Nota
                </button>

                <a href="{{ route('home') }}" class="btn-outline-home">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
            </div>

        </div>

    </div>
</div>
@endsection

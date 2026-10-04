@extends('layouts.app')

@section('content')
<div class="checkout-page checkout-page--success">
    <div class="container checkout-container checkout-container--narrow">

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
                        Jika sudah melakukan scan dan transfer via QRIS sebesar <strong class="text-accent">{{ $order->formatted_total }}</strong>, simpan bukti tangkapan layar pembayaranmu dan konfirmasi ke admin kami.
                    </p>
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
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>{{ $order->product_name }}</strong></td>
                                <td class="text-center">{{ $order->items_count }}x</td>
                                <td class="text-right">{{ $order->formatted_unit_price }}</td>
                                <td class="text-right">Rp {{ number_format($order->unit_price * $order->items_count, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Ongkos Kirim:</td>
                                <td class="text-right">{{ $order->formatted_shipping_fee }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Biaya Layanan:</td>
                                <td class="text-right">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>Total Pembayaran:</strong></td>
                                <td class="text-right text-primary"><strong>{{ $order->formatted_total }}</strong></td>
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

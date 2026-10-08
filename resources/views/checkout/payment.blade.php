@extends('layouts.app')

@section('content')
<div class="checkout-page">
    <div class="container checkout-container">

        {{-- Tombol Navigasi Kembali --}}
        <div class="checkout-top-bar">
            <a href="{{ route('home') }}#products" class="btn-checkout-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Pilihan Menu</span>
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
            <div class="step-line step-line--active"></div>
            <div class="step-item step-item--active">
                <div class="step-item__circle">2</div>
                <div class="step-item__text">
                    <span class="step-num">Langkah 2</span>
                    <span class="step-label">Detail Order & Bayar</span>
                </div>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-item__circle">3</div>
                <div class="step-item__text">
                    <span class="step-num">Langkah 3</span>
                    <span class="step-label">Selesai</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success-banner">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="checkout-grid">
            {{-- Kolom Kiri: Detail Pengiriman & Pilihan Metode Pembayaran --}}
            <div class="checkout-main">

                {{-- Card 1: Data Pengiriman & Pemesan --}}
                <div class="checkout-card">
                    <div class="checkout-card__header">
                        <div class="checkout-card__title-wrap">
                            <i class="fa-solid fa-truck-fast checkout-card__icon"></i>
                            <div>
                                <h3 class="checkout-card__title">Informasi Pengiriman</h3>
                                <p class="checkout-card__desc">Data penerima dan alamat pengantaran makananmu.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-text-edit" id="btnOpenEditAddress">
                            <i class="fa-solid fa-pen-to-square"></i> Ubah Data
                        </button>
                    </div>

                    <div class="delivery-info-box" id="deliveryInfoDisplay">
                        <div class="delivery-info-row">
                            <span class="info-label">Nama Penerima</span>
                            <span class="info-value" id="displayCustomerName">{{ $order->customer_name }}</span>
                        </div>
                        <div class="delivery-info-row">
                            <span class="info-label">No. WhatsApp / HP</span>
                            <span class="info-value" id="displayCustomerPhone">{{ $order->customer_phone }}</span>
                        </div>
                        @if ($order->customer_email)
                            <div class="delivery-info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value" id="displayCustomerEmail">{{ $order->customer_email }}</span>
                            </div>
                        @endif
                        <div class="delivery-info-row">
                            <span class="info-label">Alamat Antar</span>
                            <span class="info-value address-highlight" id="displayShippingAddress">{{ $order->shipping_address }}</span>
                        </div>
                        @if ($order->order_notes)
                            <div class="delivery-info-row">
                                <span class="info-label">Catatan Pesanan</span>
                                <span class="info-value note-highlight" id="displayOrderNotes">"{{ $order->order_notes }}"</span>
                            </div>
                        @endif
                    </div>

                    {{-- Form Edit Data Pengiriman (Tersembunyi, Toggle via Tombol) --}}
                    <div class="delivery-edit-form" id="deliveryEditForm" style="display: none;">
                        <form action="{{ route('checkout.update', $order->order_number) }}" method="POST" id="formUpdateAddress">
                            @csrf
                            @method('PUT')
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="customer_name" class="form-control" value="{{ $order->customer_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">No. WhatsApp / HP</label>
                                    <input type="tel" name="customer_phone" class="form-control" value="{{ $order->customer_phone }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Pengiriman</label>
                                <textarea name="shipping_address" rows="2" class="form-control" required>{{ $order->shipping_address }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan Pesanan</label>
                                <input type="text" name="order_notes" class="form-control" value="{{ $order->order_notes }}">
                            </div>
                            <div class="edit-form-actions">
                                <button type="button" class="btn-secondary btn-sm" id="btnCancelEditAddress">Batal</button>
                                <button type="submit" class="btn-primary btn-sm">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Card 2: Pemilihan Metode Pembayaran --}}
                <div class="checkout-card">
                    <div class="checkout-card__header">
                        <div class="checkout-card__title-wrap">
                            <i class="fa-solid fa-credit-card checkout-card__icon"></i>
                            <div>
                                <h3 class="checkout-card__title">Pilih Metode Pembayaran</h3>
                                <p class="checkout-card__desc">Pilih opsi pembayaran yang paling mudah dan nyaman untukmu.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('checkout.confirm', $order->order_number) }}" method="POST" id="formPaymentMethod">
                        @csrf
                        <div class="payment-options">

                            {{-- Option 1: Transfer Bank --}}
                            <label class="payment-option-card payment-option-card--active" for="methodBank">
                                <div class="payment-option-card__radio">
                                    <input type="radio" name="payment_method" id="methodBank" value="bca" checked>
                                    <span class="custom-radio"></span>
                                </div>
                                <div class="payment-option-card__content">
                                    <div class="payment-option-card__head">
                                        <div class="payment-option-card__title">
                                            <i class="fa-solid fa-building-columns text-primary"></i>
                                            <span>Transfer Bank (BCA / Mandiri / BRI)</span>
                                        </div>
                                    </div>
                                    <p class="payment-option-card__desc">Transfer langsung ke rekening resmi toko via ATM, Mobile Banking, atau Internet Banking.</p>

                                    {{-- Sub-pilihan Bank --}}
                                    <div class="bank-suboptions" id="bankSuboptions">
                                        <div class="bank-tabs">
                                            <button type="button" class="bank-tab bank-tab--active" data-bank="bca">BCA</button>
                                            <button type="button" class="bank-tab" data-bank="mandiri">Mandiri</button>
                                            <button type="button" class="bank-tab" data-bank="bri">BRI</button>
                                        </div>

                                        @foreach ($bankAccounts as $key => $bank)
                                            <div class="bank-detail-box {{ $loop->first ? 'bank-detail-box--active' : '' }}" id="bank-box-{{ $key }}">
                                                <div class="bank-detail-row">
                                                    <div>
                                                        <div class="bank-name">{{ $bank['name'] }}</div>
                                                        <div class="account-num" id="accNum-{{ $key }}">{{ $bank['account_number'] }}</div>
                                                        <div class="account-holder">a.n. <strong>{{ $bank['account_holder'] }}</strong></div>
                                                    </div>
                                                    <button type="button" class="btn-copy-acc" data-clipboard="{{ $bank['account_number'] }}">
                                                        <i class="fa-regular fa-copy"></i> Salin Rekening
                                                    </button>
                                                </div>
                                                <div class="bank-instruction">
                                                    <i class="fa-solid fa-circle-info"></i>
                                                    <span>Cantumkan nomor pesanan <strong>{{ $order->order_number }}</strong> pada berita transfer.</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </label>

                            {{-- Option 2: QRIS --}}
                            <label class="payment-option-card" for="methodQris">
                                <div class="payment-option-card__radio">
                                    <input type="radio" name="payment_method" id="methodQris" value="qris">
                                    <span class="custom-radio"></span>
                                </div>
                                <div class="payment-option-card__content">
                                    <div class="payment-option-card__head">
                                        <div class="payment-option-card__title">
                                            <i class="fa-solid fa-qrcode text-accent"></i>
                                            <span>QRIS (Semua E-Wallet & M-Banking)</span>
                                        </div>
                                    </div>
                                    <p class="payment-option-card__desc">Scan QRIS menggunakan GoPay, OVO, Dana, ShopeePay, LinkAja, atau aplikasi m-Banking manapun.</p>
                                </div>
                            </label>

                            {{-- Option 3: COD (Cash on Delivery) --}}
                            <label class="payment-option-card" for="methodCod">
                                <div class="payment-option-card__radio">
                                    <input type="radio" name="payment_method" id="methodCod" value="cod">
                                    <span class="custom-radio"></span>
                                </div>
                                <div class="payment-option-card__content">
                                    <div class="payment-option-card__head">
                                        <div class="payment-option-card__title">
                                            <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                                            <span>Cash on Delivery (COD / Bayar di Tempat)</span>
                                        </div>
                                    </div>
                                    <p class="payment-option-card__desc">Bayar tunai langsung ke kurir saat pesanan hangatmu tiba di depan pintu.</p>

                                    <div class="cod-info-box" id="codInfoBox" style="display: none;">
                                        <div class="cod-info-alert">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            <div>
                                                <strong>Ketentuan Bayar di Tempat (COD):</strong>
                                                <p>Harap pastikan nomor WhatsApp aktif untuk konfirmasi kurir pengantar. Mohon siapkan uang pas sebesar <strong id="codTotalText">{{ $order->formatted_total }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                        </div>

                        {{-- Tombol Konfirmasi Selesai & Tombol Kembali --}}
                        <div class="checkout-submit-wrap">
                            <button type="submit" class="btn-primary btn-checkout-submit">
                                <span>Konfirmasi & Selesaikan Pesanan</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                            <a href="{{ route('home') }}#products" class="btn-checkout-back-secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Kembali / Ubah Pilihan Cemilan</span>
                            </a>
                            <p class="checkout-guarantee-text">
                                <i class="fa-solid fa-lock"></i> Transaksi aman & data pengiriman kamu terjamin kerahasiaannya.
                            </p>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Kolom Kanan: Detail Orderan & Pengaturan Jumlah Pesanan --}}
            <div class="checkout-sidebar">
                <div class="checkout-card checkout-card--summary">
                    <div class="checkout-card__header">
                        <h3 class="checkout-card__title">Detail Pesanan</h3>
                        <span class="order-number-pill">{{ $order->order_number }}</span>
                    </div>

                    {{-- Item Pesanan dengan Pengatur Kuantitas Interaktif --}}
                    <div class="order-item-detail">
                        <div class="order-item-detail__img-wrap">
                            @if ($order->product)
                                <img src="{{ $order->product->image_url }}" alt="{{ $order->product_name }}" loading="lazy">
                            @else
                                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&fm=webp&q=80" alt="{{ $order->product_name }}" loading="lazy">
                            @endif
                        </div>
                        <div class="order-item-detail__info">
                            <h4 class="order-item-detail__name">{{ $order->product_name }}</h4>
                            <div class="order-item-detail__price">{{ $order->formatted_unit_price }} / porsi</div>

                            {{-- Kontrol Pengatur Kuantitas Interaktif --}}
                            <div class="order-item-detail__stepper">
                                <span class="stepper-label">Atur Jumlah:</span>
                                <div class="stepper-controls">
                                    <button type="button" class="stepper-btn" id="btnQtyMinus" aria-label="Kurangi">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" id="inputQty" value="{{ $order->items_count }}" min="1" max="99" class="stepper-input" readonly>
                                    <button type="button" class="stepper-btn" id="btnQtyPlus" aria-label="Tambah">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="price-breakdown">
                        <div class="price-breakdown__row">
                            <span class="price-breakdown__label">Subtotal Produk (<span id="summaryQty">{{ $order->items_count }}</span>x)</span>
                            <span class="price-breakdown__val" id="summarySubtotal">
                                Rp {{ number_format($order->unit_price * $order->items_count, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="price-breakdown__row">
                            <span class="price-breakdown__label">Ongkos Kirim (Flat)</span>
                            <span class="price-breakdown__val">{{ $order->formatted_shipping_fee }}</span>
                        </div>
                        <div class="price-breakdown__row">
                            <span class="price-breakdown__label">Biaya Layanan</span>
                            <span class="price-breakdown__val">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-breakdown__divider"></div>
                        <div class="price-breakdown__row price-breakdown__row--total">
                            <div>
                                <span class="total-title">Total Tagihan</span>
                                <span class="total-subtitle">Termasuk pajak & biaya kirim</span>
                            </div>
                            <span class="total-price" id="summaryTotal">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    {{-- Keunggulan Layanan --}}
                    <div class="checkout-perks">
                        <div class="perk-item">
                            <i class="fa-solid fa-box-open text-primary"></i>
                            <span>Kemasan higienis & kedap udara</span>
                        </div>
                        <div class="perk-item">
                            <i class="fa-solid fa-bolt text-accent"></i>
                            <span>Diantar cepat selagi masih fresh & hangat</span>
                        </div>
                        <div class="perk-item">
                            <i class="fa-solid fa-shield-check text-success"></i>
                            <span>Garansi rasa puas 100% dari pedagang lokal</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Toast Notification Salin Rekening --}}
<div class="copy-toast" id="copyToast" role="alert" aria-live="polite">
    <i class="fa-solid fa-circle-check"></i>
    <span>Nomor rekening berhasil disalin!</span>
</div>

{{-- Script Interaktif Halaman Pembayaran --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateUrl = "{{ route('checkout.update', $order->order_number) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 1. Toggle Edit Alamat Pengiriman
    const btnOpenEdit = document.getElementById('btnOpenEditAddress');
    const btnCancelEdit = document.getElementById('btnCancelEditAddress');
    const displayBox = document.getElementById('deliveryInfoDisplay');
    const editForm = document.getElementById('deliveryEditForm');

    if (btnOpenEdit && editForm && displayBox) {
        btnOpenEdit.addEventListener('click', () => {
            displayBox.style.display = 'none';
            editForm.style.display = 'block';
        });
        btnCancelEdit.addEventListener('click', () => {
            editForm.style.display = 'none';
            displayBox.style.display = 'block';
        });
    }

    // 2. Radio Metode Pembayaran Interaktif (Transfer Bank, QRIS, COD)
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const paymentCards = document.querySelectorAll('.payment-option-card');
    const bankSuboptions = document.getElementById('bankSuboptions');
    const qrisBox = document.getElementById('qrisDisplayBox');
    const codBox = document.getElementById('codInfoBox');

    function updatePaymentVisibility(selectedMethod) {
        paymentCards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.value === selectedMethod) {
                card.classList.add('payment-option-card--active');
            } else if (['bca', 'mandiri', 'bri'].includes(selectedMethod) && radio && ['bca', 'mandiri', 'bri'].includes(radio.value)) {
                card.classList.add('payment-option-card--active');
            } else {
                card.classList.remove('payment-option-card--active');
            }
        });

        if (['bca', 'mandiri', 'bri'].includes(selectedMethod)) {
            if (bankSuboptions) bankSuboptions.style.display = 'block';
            if (qrisBox) qrisBox.style.display = 'none';
            if (codBox) codBox.style.display = 'none';
        } else if (selectedMethod === 'qris') {
            if (bankSuboptions) bankSuboptions.style.display = 'none';
            if (qrisBox) qrisBox.style.display = 'flex';
            if (codBox) codBox.style.display = 'none';
        } else if (selectedMethod === 'cod') {
            if (bankSuboptions) bankSuboptions.style.display = 'none';
            if (qrisBox) qrisBox.style.display = 'none';
            if (codBox) codBox.style.display = 'block';
        }
    }

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            updatePaymentVisibility(this.value);
        });
    });

    // Sub-tab Bank (BCA, Mandiri, BRI)
    const bankTabs = document.querySelectorAll('.bank-tab');
    const bankBoxes = document.querySelectorAll('.bank-detail-box');
    const methodBankRadio = document.getElementById('methodBank');

    bankTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const targetBank = this.getAttribute('data-bank');
            bankTabs.forEach(t => t.classList.remove('bank-tab--active'));
            this.classList.add('bank-tab--active');

            bankBoxes.forEach(box => {
                box.classList.toggle('bank-detail-box--active', box.id === 'bank-box-' + targetBank);
            });

            if (methodBankRadio) {
                methodBankRadio.value = targetBank;
                methodBankRadio.checked = true;
            }
        });
    });

    // 3. Salin Nomor Rekening ke Clipboard
    const copyBtns = document.querySelectorAll('.btn-copy-acc');
    const toast = document.getElementById('copyToast');

    copyBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const num = this.getAttribute('data-clipboard');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(num).then(() => {
                    showToast('Nomor rekening ' + num + ' berhasil disalin!');
                });
            } else {
                showToast('Nomor rekening disalin!');
            }
        });
    });

    function showToast(msg) {
        if (!toast) return;
        toast.querySelector('span').textContent = msg;
        toast.classList.add('copy-toast--visible');
        setTimeout(() => {
            toast.classList.remove('copy-toast--visible');
        }, 2600);
    }

    // 4. Interaktif Pengatur Kuantitas Pesanan (+) dan (-)
    const btnMinus = document.getElementById('btnQtyMinus');
    const btnPlus = document.getElementById('btnQtyPlus');
    const inputQty = document.getElementById('inputQty');
    const summaryQty = document.getElementById('summaryQty');
    const summarySubtotal = document.getElementById('summarySubtotal');
    const summaryTotal = document.getElementById('summaryTotal');
    const codTotalText = document.getElementById('codTotalText');

    function sendQtyUpdate(newQty) {
        if (!csrfToken) return;

        fetch(updateUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ items_count: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (summaryQty) summaryQty.textContent = data.items_count;
                if (summarySubtotal) summarySubtotal.textContent = data.subtotal;
                if (summaryTotal) summaryTotal.textContent = data.total_amount;
                if (codTotalText) codTotalText.textContent = data.total_amount;
            }
        })
        .catch(err => console.error('Gagal memperbarui kuantitas:', err));
    }

    if (btnMinus && btnPlus && inputQty) {
        btnMinus.addEventListener('click', () => {
            let current = parseInt(inputQty.value) || 1;
            if (current > 1) {
                current--;
                inputQty.value = current;
                sendQtyUpdate(current);
            }
        });

        btnPlus.addEventListener('click', () => {
            let current = parseInt(inputQty.value) || 1;
            if (current < 99) {
                current++;
                inputQty.value = current;
                sendQtyUpdate(current);
            }
        });
    }
});
</script>
@endsection

{{-- Modal Formulir Pemesanan & Pengiriman (Langkah 1) --}}
<div class="order-modal-backdrop" id="orderModalBackdrop" data-lenis-prevent aria-hidden="true">
    <div class="order-modal" data-lenis-prevent role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        {{-- Header Modal (Fixed / Non-scroll) --}}
        <div class="order-modal__header">
            <div class="order-modal__header-left">
                <h3 class="order-modal__title" id="orderModalTitle">Formulir Pemesanan</h3>
                <p class="order-modal__subtitle">Lengkapi data diri dan alamat pengiriman pesananmu.</p>
            </div>
            <button type="button" class="order-modal__close" id="orderModalClose" aria-label="Tutup modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Form Modal --}}
        <form action="{{ route('checkout.store') }}" method="POST" id="orderModalForm" class="order-modal__form" data-lenis-prevent>
            @csrf
            <input type="hidden" name="product_id" id="modalProductId" value="">

            {{-- Bagian yang dapat di-scroll (Body Modal) --}}
            <div class="order-modal__scrollable-body" id="modalScrollableBody" data-lenis-prevent>
                {{-- Ringkasan Produk Terpilih --}}
                <div class="order-product-card">
                    <div class="order-product-card__thumb">
                        <img id="modalProductImg" src="" alt="Produk" loading="lazy">
                    </div>
                    <div class="order-product-card__info">
                        <span class="order-product-card__tag" id="modalProductCategory">Cemilan</span>
                        <h4 class="order-product-card__name" id="modalProductName">Nama Produk</h4>
                        <div class="order-product-card__price" id="modalProductPrice">Rp 0</div>
                    </div>
                    <div class="order-product-card__qty">
                        <label for="modalProductQty" class="order-qty-label">Jumlah</label>
                        <div class="order-qty-control">
                            <button type="button" class="order-qty-btn" id="modalQtyMinus" aria-label="Kurangi jumlah">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" name="items_count" id="modalProductQty" value="1" min="1" max="99" class="order-qty-input" readonly>
                            <button type="button" class="order-qty-btn" id="modalQtyPlus" aria-label="Tambah jumlah">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="order-modal__fields">
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="customerName" class="form-label">
                                <i class="fa-solid fa-user"></i> Nama Lengkap <span class="required">*</span>
                            </label>
                            <input type="text" name="customer_name" id="customerName" class="form-control"
                                   placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="form-group">
                            <label for="customerPhone" class="form-label">
                                <i class="fa-solid fa-phone"></i> No. WhatsApp / HP <span class="required">*</span>
                            </label>
                            <input type="tel" name="customer_phone" id="customerPhone" class="form-control"
                                   placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customerEmail" class="form-label">
                            <i class="fa-solid fa-envelope"></i> Email <span class="optional">(Opsional, untuk nota pesanan)</span>
                        </label>
                        <input type="email" name="customer_email" id="customerEmail" class="form-control"
                               placeholder="Contoh: budi@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="shippingAddress" class="form-label">
                            <i class="fa-solid fa-location-dot"></i> Alamat Pengiriman Lengkap <span class="required">*</span>
                        </label>
                        <textarea name="shipping_address" id="shippingAddress" rows="3" class="form-control"
                                  placeholder="Tuliskan nama jalan, RT/RW, nomor rumah, kelurahan, kecamatan, dan patokan..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="orderNotes" class="form-label">
                            <i class="fa-solid fa-note-sticky"></i> Catatan Pesanan <span class="optional">(Opsional)</span>
                        </label>
                        <input type="text" name="order_notes" id="orderNotes" class="form-control"
                               placeholder="Contoh: Sambal dipisah ya min / Pagar hitam seberang masjid">
                    </div>
                </div>
            </div>

            {{-- Estimasi Total & Tombol Submit (Fixed di Bawah Modal) --}}
            <div class="order-modal__footer">
                <div class="order-modal__total-preview">
                    <span class="total-label">Estimasi Subtotal:</span>
                    <span class="total-value" id="modalSubtotalPreview">Rp 0</span>
                </div>
                <div class="order-modal__actions">
                    <button type="button" class="btn-secondary" id="orderModalCancel">Batal</button>
                    <button type="submit" class="btn-primary order-modal__submit-btn">
                        <span>Lanjut ke Pembayaran</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@extends('layouts.admin')

@section('title', 'Pengaturan Toko')

@section('content')
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Pengaturan Toko</h1>
            <p class="page-subtitle">Konfigurasi brand, ongkir, rekening, dan metode pembayaran. Nilai ini dipakai langsung oleh halaman checkout.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="content-card">
            <div class="content-card-header">
                <h2 class="card-title">Informasi Brand & Kontak</h2>
            </div>
            <div style="padding: 20px;">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="brand_name">Nama Brand</label>
                        <input type="text" name="brand_name" id="brand_name" class="form-input"
                               value="{{ old('brand_name', $settings['brand_name']) }}" maxlength="100">
                        @error('brand_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="brand_tagline">Tagline</label>
                        <input type="text" name="brand_tagline" id="brand_tagline" class="form-input"
                               value="{{ old('brand_tagline', $settings['brand_tagline']) }}" maxlength="200">
                        @error('brand_tagline') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="wa_number">Nomor WhatsApp (tanpa tanda +)</label>
                        <input type="text" name="wa_number" id="wa_number" class="form-input"
                               value="{{ old('wa_number', $settings['wa_number']) }}" placeholder="6281234567890">
                        @error('wa_number') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="support_email">Email Support</label>
                        <input type="email" name="support_email" id="support_email" class="form-input"
                               value="{{ old('support_email', $settings['support_email']) }}">
                        @error('support_email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="support_address">Alamat Toko</label>
                    <textarea name="support_address" id="support_address" class="form-textarea" rows="2">{{ old('support_address', $settings['support_address']) }}</textarea>
                    @error('support_address') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="content-card" style="margin-top: 20px;">
            <div class="content-card-header">
                <h2 class="card-title">Ongkir & Biaya</h2>
            </div>
            <div style="padding: 20px;">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="shipping_fee">Biaya Pengiriman (Rp)</label>
                        <input type="number" name="shipping_fee" id="shipping_fee" class="form-input" min="0"
                               value="{{ old('shipping_fee', $settings['shipping_fee']) }}">
                        @error('shipping_fee') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="service_fee">Biaya Layanan (Rp)</label>
                        <input type="number" name="service_fee" id="service_fee" class="form-input" min="0"
                               value="{{ old('service_fee', $settings['service_fee']) }}">
                        @error('service_fee') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="free_shipping_threshold">Gratis Ongkir Mulai Belanja (Rp)</label>
                    <input type="number" name="free_shipping_threshold" id="free_shipping_threshold" class="form-input" min="0"
                           value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}">
                    <small style="color:#94a3b8; font-size:12px;">Isi 0 untuk menonaktifkan gratis ongkir.</small>
                    @error('free_shipping_threshold') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="content-card" style="margin-top: 20px;">
            <div class="content-card-header">
                <h2 class="card-title">Rekening Pembayaran</h2>
            </div>
            <div style="padding: 20px;">
                @foreach(['bca' => 'BCA', 'mandiri' => 'Mandiri', 'bri' => 'BRI'] as $key => $label)
                    <div class="form-row-2" style="margin-bottom: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="bank_{{ $key }}_number">No. Rekening {{ $label }}</label>
                            <input type="text" name="bank_{{ $key }}_number" id="bank_{{ $key }}_number" class="form-input"
                                   value="{{ old("bank_{$key}_number", $settings["bank_{$key}_number"]) }}">
                            @error("bank_{$key}_number") <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bank_{{ $key }}_holder">Atas Nama {{ $label }}</label>
                            <input type="text" name="bank_{{ $key }}_holder" id="bank_{{ $key }}_holder" class="form-input"
                                   value="{{ old("bank_{$key}_holder", $settings["bank_{$key}_holder"]) }}">
                            @error("bank_{$key}_holder") <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endforeach

                <div class="form-group">
                    <label class="form-label" for="qris_payload">QRIS Payload / URL</label>
                    <input type="text" name="qris_payload" id="qris_payload" class="form-input"
                           value="{{ old('qris_payload', $settings['qris_payload']) }}"
                           placeholder="https://qr.dcemilinyuk.id/pay">
                    @error('qris_payload') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:#334155;">
                    <input type="checkbox" name="cod_enabled" value="1" style="width:16px; height:16px;"
                           {{ old('cod_enabled', $settings['cod_enabled']) ? 'checked' : '' }}>
                    Aktifkan Cash on Delivery (COD)
                </label>
            </div>
        </div>

        <div style="margin-top: 20px; display:flex; gap:12px; align-items:center;">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk"></i>&nbsp; Simpan Pengaturan
            </button>
            <a href="{{ route('admin.settings.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
@endsection
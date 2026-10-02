@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')
    {{-- Header --}}
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Riwayat Pesanan</h1>
            <p class="page-subtitle">Pantau dan kelola seluruh transaksi pesanan pelanggan yang masuk.</p>
        </div>
        <div class="page-actions">
            <div class="last-update-tag">
                <i class="fa-regular fa-clock"></i> Last Update: Hari Ini, 14:30
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="toolbar-row">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="toolbar-left">
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Cari No Pesanan / Pelanggan..." 
                       value="{{ request('search') }}">
            </div>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Status: Semua</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="content-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NO PESANAN</th>
                        <th>PELANGGAN</th>
                        <th>TANGGAL</th>
                        <th>ITEMS</th>
                        <th>PRODUK</th>
                        <th>JUMLAH</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="text-bold">{{ $order->order_number }}</td>
                            <td class="customer-info-cell">
                                <div class="cust-name">{{ $order->customer_name }}</div>
                                <div class="cust-email">{{ $order->customer_email }}</div>
                            </td>
                            <td>{{ $order->order_date }}</td>
                            <td>{{ $order->items_count }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td class="price-text">{{ $order->formatted_total }}</td>
                            <td>
                                <button type="button" 
                                        class="badge {{ $order->status_badge_class }}" 
                                        style="border: none; cursor: pointer;"
                                        title="Klik untuk ubah status"
                                        onclick="openStatusModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->status }}')">
                                    {{ $order->status }} <i class="fa-solid fa-chevron-down" style="font-size: 9px; margin-left: 4px;"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 36px;">
                                Tidak ada data pesanan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="table-footer-row">
            <div class="table-footer-info">
                Menampilkan {{ $orders->count() }} dari {{ $totalOrdersCount }} Pesanan
            </div>
            <div class="table-footer-pagination">
                @if ($orders->onFirstPage())
                    <button class="btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed;">Sebelumnya</button>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="btn-secondary">Sebelumnya</a>
                @endif

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="btn-primary" style="padding: 9px 20px;">Selanjutnya</a>
                @else
                    <button class="btn-primary" disabled style="opacity: 0.6; cursor: not-allowed; padding: 9px 20px;">Selanjutnya</button>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    {{-- Modal Ubah Status --}}
    <div class="modal-backdrop" id="modalChangeStatus">
        <div class="modal-box" style="max-width: 420px;">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Status Pesanan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalChangeStatus')">&times;</button>
            </div>
            <form id="formChangeStatus" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">
                        Pilih status baru untuk pesanan <strong id="modalOrderNumber" style="color: #0f172a;"></strong>:
                    </p>
                    <div class="form-group">
                        <label class="form-label" for="orderStatusSelect">Status Pesanan</label>
                        <select name="status" id="orderStatusSelect" class="form-select-full">
                            <option value="Diproses">Diproses</option>
                            <option value="Dikirim">Dikirim</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalChangeStatus')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openStatusModal(orderId, orderNumber, currentStatus) {
        document.getElementById('modalOrderNumber').textContent = orderNumber;
        document.getElementById('orderStatusSelect').value = currentStatus;
        document.getElementById('formChangeStatus').action = `/admin/orders/${orderId}/status`;
        openModal('modalChangeStatus');
    }
</script>
@endpush

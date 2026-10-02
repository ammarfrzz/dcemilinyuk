@extends('layouts.admin')

@section('title', 'Manajemen Persediaan')

@section('content')
    {{-- Header --}}
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Persediaan</h1>
            <p class="page-subtitle">Pantau jumlah persediaan barang dan tentukan ambang batas aman produk.</p>
        </div>
    </div>

    {{-- 3 Summary Cards --}}
    <div class="metric-cards-grid">
        {{-- Card 1: Total Produk Terdaftar --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL PRODUK TERDAFTAR</div>
                <div class="metric-value">{{ $totalRegistered }} SKU</div>
                <div class="metric-change neutral">
                    0 perubahan <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        {{-- Card 2: Total Perlu Restock --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL PERLU RESTOCK</div>
                <div class="metric-value">{{ $totalRestockNeeded }} SKU</div>
                <div class="metric-change positive">
                    +2 item baru <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle warning-soft">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
        </div>

        {{-- Card 3: Stok Kritis --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">STOK KRITIS</div>
                <div class="metric-value">{{ $totalCritical }} SKU</div>
                <div class="metric-change positive">
                    +1 kritis <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle danger-soft">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="toolbar-row">
        <form action="{{ route('admin.stock.index') }}" method="GET" class="toolbar-left">
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Cari Nama Produk..." 
                       value="{{ request('search') }}">
            </div>
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="Semua" {{ request('category') == 'Semua' ? 'selected' : '' }}>Kategori: Semua</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Stock Table Card --}}
    <div class="content-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PRODUK</th>
                        <th>KATEGORI</th>
                        <th>STOK SAAT INI</th>
                        <th>MIN. LIMIT</th>
                        <th>STATUS STOK</th>
                        <th>TERAKHIR DIUPDATE</th>
                        <th style="text-align: center; width: 110px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $prod)
                        <tr>
                            <td class="text-bold">{{ $prod->name }}</td>
                            <td style="color: #64748b;">{{ $prod->display_category }}</td>
                            <td class="text-bold">{{ $prod->stock }}</td>
                            <td>{{ $prod->min_stock ?? 10 }}</td>
                            <td>
                                <span class="badge {{ $prod->stock_status_class }}">
                                    {{ $prod->stock_status_label }}
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                {{ $prod->updated_at ? $prod->updated_at->translatedFormat('d M Y, H:i') : '12 Okt 2023, 14:20' }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" 
                                        class="btn-orange-sm" 
                                        onclick="openRestockModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->stock }})">
                                    Restock
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 36px;">
                                Tidak ada produk persediaan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modals')
    {{-- Modal Quick Restock --}}
    <div class="modal-backdrop" id="modalRestock">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header">
                <h3 class="modal-title">Restock Produk</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalRestock')">&times;</button>
            </div>
            <form id="formRestock" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
                        Tambahkan persediaan stok untuk <strong id="restockProdName" style="color: #0f172a;"></strong>
                    </p>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 18px; font-size: 13.5px;">
                        Stok saat ini: <strong id="restockCurrentStock" style="color: #ff6a00;">0</strong> Pcs
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="restockQuantity">Jumlah Tambahan Stok (Pcs)</label>
                        <input type="number" name="quantity" id="restockQuantity" class="form-input" min="1" value="20" required>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                        <button type="button" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="document.getElementById('restockQuantity').value = 10;">+10</button>
                        <button type="button" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="document.getElementById('restockQuantity').value = 25;">+25</button>
                        <button type="button" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="document.getElementById('restockQuantity').value = 50;">+50</button>
                        <button type="button" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="document.getElementById('restockQuantity').value = 100;">+100</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalRestock')">Batal</button>
                    <button type="submit" class="btn-primary">Konfirmasi Restock</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRestockModal(prodId, prodName, currentStock) {
        document.getElementById('restockProdName').textContent = prodName;
        document.getElementById('restockCurrentStock').textContent = currentStock;
        document.getElementById('formRestock').action = `/admin/stock/${prodId}/restock`;
        openModal('modalRestock');
    }
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
    {{-- Header --}}
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Produk</h1>
            <p class="page-subtitle">Daftar inventaris makanan beku lengkap dengan informasi stok dan harga.</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn-primary" onclick="openModal('modalAddProduct')">
                <i class="fa-solid fa-plus"></i> Tambah Produk
            </button>
        </div>
    </div>

    {{-- Filter Toolbar (Optional search & category filter) --}}
    <div class="toolbar-row">
        <form action="{{ route('admin.products.index') }}" method="GET" class="toolbar-left">
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Cari nama produk atau SKU..." 
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

    {{-- Table Card --}}
    <div class="content-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">FOTO</th>
                        <th>PRODUK & SKU</th>
                        <th>KATEGORI</th>
                        <th>HARGA SATUAN</th>
                        <th>STOK</th>
                        <th>STATUS</th>
                        <th style="text-align: center; width: 80px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                @if ($product->image_path && file_exists(public_path('images/' . $product->image_path)))
                                    <img src="{{ asset('images/' . $product->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-photo-thumb">
                                @else
                                    <div class="product-photo-thumb">
                                        <i class="fa-solid fa-utensils"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="product-info-cell">
                                <div class="prod-title">{{ $product->name }}</div>
                                <div class="prod-sku">{{ $product->sku ?? 'SKU-00' . $product->id }}</div>
                            </td>
                            <td>{{ $product->display_category }}</td>
                            <td class="price-text">{{ $product->formatted_price }}</td>
                            <td>{{ $product->stock }} Pcs</td>
                            <td>
                                <span class="badge {{ $product->status_class }}">
                                    {{ $product->status_label }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" 
                                        class="btn-icon-edit" 
                                        title="Edit Produk"
                                        onclick="openEditProductModal({{ json_encode($product) }})">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 36px;">
                                Belum ada data produk tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="table-footer-row">
            <div class="table-footer-info">
                Menampilkan {{ $products->count() }} dari {{ $totalProductsCount }} Produk
            </div>
            <div class="table-footer-pagination">
                @if ($products->onFirstPage())
                    <button class="btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed;">Sebelumnya</button>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="btn-secondary">Sebelumnya</a>
                @endif

                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="btn-primary" style="padding: 9px 20px;">Selanjutnya</a>
                @else
                    <button class="btn-primary" disabled style="opacity: 0.6; cursor: not-allowed; padding: 9px 20px;">Selanjutnya</button>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    {{-- Modal Tambah Produk --}}
    <div class="modal-backdrop" id="modalAddProduct">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Produk Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddProduct')">&times;</button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="add_name">Nama Produk</label>
                        <input type="text" name="name" id="add_name" class="form-input" placeholder="Contoh: Nugget Ayam Premium" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="add_sku">SKU (Opsional)</label>
                            <input type="text" name="sku" id="add_sku" class="form-input" placeholder="Contoh: NUG-001">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_category">Kategori</label>
                            <select name="category_name" id="add_category" class="form-select-full" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="add_price">Harga Satuan (Rp)</label>
                            <input type="number" name="price" id="add_price" class="form-input" placeholder="Contoh: 48000" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_stock">Stok Awal</label>
                            <input type="number" name="stock" id="add_stock" class="form-input" placeholder="Contoh: 45" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="add_min_stock">Ambang Batas Minimum Restock</label>
                        <input type="number" name="min_stock" id="add_min_stock" class="form-input" value="10" min="1">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="add_desc">Deskripsi Singkat</label>
                        <textarea name="description" id="add_desc" class="form-textarea" rows="3" placeholder="Informasi rasa, bahan, dan kemasan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddProduct')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Produk --}}
    <div class="modal-backdrop" id="modalEditProduct">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Data Produk</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditProduct')">&times;</button>
            </div>
            <form id="formEditProduct" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_name">Nama Produk</label>
                        <input type="text" name="name" id="edit_name" class="form-input" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_sku">SKU</label>
                            <input type="text" name="sku" id="edit_sku" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_category">Kategori</label>
                            <select name="category_name" id="edit_category" class="form-select-full" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_price">Harga Satuan (Rp)</label>
                            <input type="number" name="price" id="edit_price" class="form-input" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_stock">Jumlah Stok</label>
                            <input type="number" name="stock" id="edit_stock" class="form-input" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_min_stock">Ambang Batas Minimum</label>
                        <input type="number" name="min_stock" id="edit_min_stock" class="form-input" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_desc">Deskripsi Singkat</label>
                        <textarea name="description" id="edit_desc" class="form-textarea" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditProduct')">Batal</button>
                    <button type="submit" class="btn-primary">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openEditProductModal(product) {
        document.getElementById('edit_name').value = product.name || '';
        document.getElementById('edit_sku').value = product.sku || '';
        document.getElementById('edit_category').value = product.category_name || '';
        document.getElementById('edit_price').value = product.price || 0;
        document.getElementById('edit_stock').value = product.stock || 0;
        document.getElementById('edit_min_stock').value = product.min_stock || 10;
        document.getElementById('edit_desc').value = product.description || '';
        document.getElementById('formEditProduct').action = `/admin/products/${product.id}`;
        openModal('modalEditProduct');
    }
</script>
@endpush

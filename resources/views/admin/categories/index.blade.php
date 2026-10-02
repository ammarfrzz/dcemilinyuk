@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')
    {{-- Header --}}
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Kategori</h1>
            <p class="page-subtitle">Atur klasifikasi kelompok cemilan dan minuman beku di toko.</p>
        </div>
        <div class="page-actions">
            <div class="category-header-badges">
                <div class="stat-pill-box">
                    <div class="stat-pill-title">TOTAL KATEGORI</div>
                    <div class="stat-pill-num">{{ $totalCount }} Kategori</div>
                </div>
                <div class="stat-pill-box">
                    <div class="stat-pill-title">KATEGORI AKTIF</div>
                    <div class="stat-pill-num green">{{ $activeCount }} Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subheader & Action Button --}}
    <div class="toolbar-row" style="margin-top: 10px; margin-bottom: 24px;">
        <div style="font-size: 14.5px; font-weight: 500; color: #475569;">
            Daftar pengelompokan produk aktif
        </div>
        <div>
            <button type="button" class="btn-primary" onclick="openModal('modalAddCategory')">
                <i class="fa-solid fa-plus"></i> Tambah Kategori
            </button>
        </div>
    </div>

    {{-- Categories Grid (3 Columns) --}}
    <div class="categories-grid">
        @forelse ($categories as $category)
            <div class="category-card">
                <div>
                    <span class="cat-code-badge">{{ $category->code }}</span>
                    <h3 class="cat-title">{{ $category->name }}</h3>
                    <p class="cat-desc">{{ $category->description }}</p>
                </div>
                <div class="cat-footer">
                    <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $category->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                    <button type="button" 
                            class="btn-icon-edit" 
                            title="Edit Kategori"
                            onclick="openEditCategoryModal({{ json_encode($category) }})">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; color: #94a3b8; padding: 48px; background: #fff; border-radius: 16px;">
                Belum ada kategori terdaftar.
            </div>
        @endforelse
    </div>
@endsection

@section('modals')
    {{-- Modal Tambah Kategori --}}
    <div class="modal-backdrop" id="modalAddCategory">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kategori Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddCategory')">&times;</button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="add_cat_name">Nama Kategori</label>
                        <input type="text" name="name" id="add_cat_name" class="form-input" placeholder="Contoh: Aneka Sosis & Bakso" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="add_cat_code">Kode Kategori (Opsional)</label>
                        <input type="text" name="code" id="add_cat_code" class="form-input" placeholder="Contoh: CAT-007">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="add_cat_desc">Deskripsi Kategori</label>
                        <textarea name="description" id="add_cat_desc" class="form-textarea" rows="4" placeholder="Jelaskan jenis dan ciri khas produk di kategori ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddCategory')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kategori --}}
    <div class="modal-backdrop" id="modalEditCategory">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Data Kategori</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditCategory')">&times;</button>
            </div>
            <form id="formEditCategory" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_cat_name">Nama Kategori</label>
                        <input type="text" name="name" id="edit_cat_name" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_cat_desc">Deskripsi Kategori</label>
                        <textarea name="description" id="edit_cat_desc" class="form-textarea" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 600; color: #334155;">
                            <input type="checkbox" name="is_active" id="edit_cat_active" value="1" style="accent-color: #ff6a00;">
                            <span>Status Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditCategory')">Batal</button>
                    <button type="submit" class="btn-primary">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openEditCategoryModal(category) {
        document.getElementById('edit_cat_name').value = category.name || '';
        document.getElementById('edit_cat_desc').value = category.description || '';
        document.getElementById('edit_cat_active').checked = !!category.is_active;
        document.getElementById('formEditCategory').action = `/admin/categories/${category.id}`;
        openModal('modalEditCategory');
    }
</script>
@endpush

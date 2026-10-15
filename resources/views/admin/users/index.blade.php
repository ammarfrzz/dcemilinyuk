@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Kelola Pengguna</h1>
            <p class="page-subtitle">Atur role, status aktif, dan akun admin toko.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openModal('modalAddUser')">
            <i class="fa-solid fa-user-plus"></i>&nbsp; Tambah Pengguna
        </button>
    </div>

    <div class="metric-cards-grid">
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL PENGGUNA</div>
                <div class="metric-value">{{ $summary['total'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-users"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">ADMIN</div>
                <div class="metric-value">{{ $summary['admins'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-user-shield"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">PELANGGAN</div>
                <div class="metric-value">{{ $summary['customers'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-basket-shopping"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">NONAKTIF</div>
                <div class="metric-value">{{ $summary['inactive'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-user-slash"></i></div>
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <h2 class="card-title">Daftar Pengguna</h2>
        </div>

        <form method="GET" class="table-responsive">
            <div class="form-row-2" style="padding: 0 20px 16px;">
                <input type="text" name="search" class="form-input" placeholder="Cari nama / email / no. HP..."
                       value="{{ request('search') }}">
                <select name="role" class="form-select-full">
                    <option value="Semua">Semua Role</option>
                    @foreach(['superadmin' => 'Super Admin', 'admin' => 'Admin', 'user' => 'Pelanggan'] as $key => $label)
                        <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select-full">
                    <option value="Semua">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="btn-primary">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>STATUS</th>
                        <th>LOGIN TERAKHIR</th>
                        <th style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="text-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->isSuperAdmin() ? 'badge-info' : ($user->isAdmin() ? 'badge-warning' : 'badge-secondary') }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>{{ $user->last_login_at?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-orange-sm"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-phone="{{ $user->phone }}"
                                        data-user-address="{{ $user->address }}"
                                        data-user-role="{{ $user->role }}"
                                        data-user-active="{{ $user->is_active ? 1 : 0 }}"
                                        onclick="editUser(this)">
                                    Edit
                                </button>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-red-sm"
                                                onclick="return confirm('Hapus pengguna {{ $user->name }}?')">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 36px;">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 20px;">
            {{ $users->links() }}
        </div>
    </div>
@endsection

@section('modals')
    {{-- Modal Tambah --}}
    <div class="modal-backdrop" id="modalAddUser">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Pengguna</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddUser')">&times;</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @include('admin.users.partials.form', ['user' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddUser')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal-backdrop" id="modalEditUser">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Pengguna</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditUser')">&times;</button>
            </div>
            <form id="formEditUser" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('admin.users.partials.form', ['user' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditUser')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function editUser(button) {
            const d = button.dataset;
            const form = document.getElementById('formEditUser');
            form.action = `{{ url('admin/users') }}/${d.userId}`;

            form.querySelector('[name="name"]').value = d.userName ?? '';
            form.querySelector('[name="email"]').value = d.userEmail ?? '';
            form.querySelector('[name="phone"]').value = d.userPhone ?? '';
            form.querySelector('[name="address"]').value = d.userAddress ?? '';
            form.querySelector('[name="role"]').value = d.userRole || 'user';
            form.querySelector('[name="is_active"]').checked = d.userActive === '1';
            form.querySelector('[name="password"]').value = '';
            form.querySelector('[name="password_confirmation"]').value = '';

            openModal('modalEditUser');
        }
    </script>
@endsection
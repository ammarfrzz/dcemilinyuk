{{-- Formulir bersama untuk tambah & edit pengguna --}}
<div class="form-row-2">
    <div class="form-group">
        <label class="form-label" for="user_name">Nama Lengkap</label>
        <input type="text" name="name" id="user_name" class="form-input" required maxlength="100">
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="user_email">Email</label>
        <input type="email" name="email" id="user_email" class="form-input" required maxlength="150">
        @error('email') <span class="form-error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-row-2">
    <div class="form-group">
        <label class="form-label" for="user_phone">Nomor HP</label>
        <input type="text" name="phone" id="user_phone" class="form-input" maxlength="25">
        @error('phone') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="user_role">Role</label>
        <select name="role" id="user_role" class="form-select-full" required>
            <option value="user">Pelanggan</option>
            <option value="admin">Admin</option>
            <option value="superadmin">Super Admin</option>
        </select>
        @error('role') <span class="form-error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="user_address">Alamat</label>
    <textarea name="address" id="user_address" class="form-textarea" rows="2" maxlength="500"></textarea>
    @error('address') <span class="form-error">{{ $message }}</span> @enderror
</div>

<div class="form-row-2">
    <div class="form-group">
        <label class="form-label" for="user_password">Password</label>
        <input type="password" name="password" id="user_password" class="form-input" minlength="8">
        <small style="color:#94a3b8; font-size:12px;">Minimal 8 karakter. Kosongkan saat edit untuk tidak mengubah.</small>
        @error('password') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="user_password_confirmation">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="user_password_confirmation" class="form-input" minlength="8">
    </div>
</div>

<div class="form-group">
    <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:#334155;">
        <input type="checkbox" name="is_active" value="1" checked style="width:16px; height:16px;">
        Akun aktif (bisa login)
    </label>
</div>
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') && $request->role !== 'Semua') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('is_active', $request->status === 'Aktif');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByRaw("CASE role WHEN 'superadmin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => User::count(),
            'admins' => User::admins()->count(),
            'customers' => User::where('role', User::ROLE_USER)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'summary'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SUPERADMIN, User::ROLE_USER])],
            'password' => ['required', 'confirmed', Password::min(8)],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'avatar.max' => 'Ukuran avatar maksimal 1MB.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => $validated['password'],
            'address' => $validated['address'] ?? null,
            'avatar' => $this->handleAvatarUpload($request),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SUPERADMIN, User::ROLE_USER])],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        // Jangan sampai ada 0 super admin aktif
        if ($user->isSuperAdmin()
            && ($validated['role'] !== User::ROLE_SUPERADMIN || empty($validated['is_active']))) {
            return back()->with('error', 'Tidak bisa menurunkan atau menonaktifkan super admin terakhir.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        if ($request->hasFile('avatar')) {
            $this->deleteStoredAvatar($user->avatar);
            $data['avatar'] = $this->handleAvatarUpload($request);
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteStoredAvatar($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->isSuperAdmin() && User::superAdmins()->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus super admin terakhir.');
        }

        $this->deleteStoredAvatar($user->avatar);
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$name}' berhasil dihapus.");
    }

    private function handleAvatarUpload(Request $request): ?string
    {
        if (! $request->hasFile('avatar')) {
            return null;
        }

        $file = $request->file('avatar');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . Str::random(6)
            . '.' . $file->getClientOriginalExtension();

        return 'storage/' . $file->storeAs('avatars', $filename, 'public');
    }

    private function deleteStoredAvatar(?string $avatar): void
    {
        if (! $avatar || ! str_starts_with($avatar, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('storage/', '', $avatar));
    }
}
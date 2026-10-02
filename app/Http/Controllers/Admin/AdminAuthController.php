<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        session([
            'admin_logged_in' => true,
            'admin_name' => 'Hendra Wijaya',
            'admin_email' => $request->email,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang kembali, Hendra Wijaya (Mode Dummy)!');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_name', 'admin_email']);
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem admin.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSession
{
    /**
     * Pastikan request berasal dari admin yang sudah login (Laravel Auth guard `web`).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->guest(route('login'))
                ->with('info', 'Silakan masuk terlebih dahulu untuk mengakses panel admin.');
        }

        if (! $user->isAdmin()) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda tidak punya akses ke panel admin.');
        }

        if (! $user->is_active) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi super admin.');
        }

        return $next($request);
    }
}
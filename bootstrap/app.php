<?php

use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Semua endpoint /api selalu merespons JSON, termasuk pesan error
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminSession::class,
            'superadmin' => EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Endpoint API tidak boleh dirender sebagai halaman HTML
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        // Bersihkan pesan 404 supaya tidak membocorkan nama model / trace debug.
        // Catatan: ModelNotFoundException sudah diubah Laravel menjadi
        // NotFoundHttpException sebelum callback render dipanggil.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            // Pesan bawaan Laravel ("No query results for model ...") terlalu
            // teknis untuk disembunyikan dari pengguna API.
            return response()->json([
                'message' => $e->getMessage() !== ''
                    ? 'Data yang Anda cari tidak ditemukan.'
                    : 'Endpoint tidak ditemukan.',
            ], 404);
        });
    })->create();
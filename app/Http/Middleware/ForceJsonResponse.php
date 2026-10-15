<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan semua request ke prefix /api selalu mendapat respons JSON,
 * termasuk saat validasi gagal atau terjadi exception.
 *
 * Tanpa ini, klien yang tidak mengirim header `Accept: application/json`
 * akan menerima halaman HTML (atau redirect) untuk error 422/404/500.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        /** @var Response $response */
        $response = $next($request);

        if (! $response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
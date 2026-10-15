<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Endpoint JSON untuk katalog & pembuatan pesanan. Dibatasi rate limiter
| bawaan Laravel (60 request/menit per IP) lewat middleware group `api`.
|
*/

// Katalog (public, read-only)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/settings', [SettingController::class, 'public']);

// Pembuatan pesanan
Route::post('/orders', [OrderController::class, 'store']);

// Lacak status pesanan (butuh order number + email/nomor HP sebagai verifikasi ringan)
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
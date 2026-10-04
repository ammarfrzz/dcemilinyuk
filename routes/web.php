<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public Storefront
Route::get('/', [HomeController::class, 'index'])->name('home');

// Checkout & Multi-Step Payment Flow
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pembayaran/{order_number}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::put('/pembayaran/{order_number}', [CheckoutController::class, 'update'])->name('checkout.update');
Route::post('/pembayaran/{order_number}/konfirmasi', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
Route::get('/pesanan-berhasil/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

// Admin Authentication
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes (Wajib Login Admin)
Route::prefix('admin')->middleware('admin.auth')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // 1. Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Riwayat Pesanan
    Route::get('/riwayat-pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders', [AdminOrderController::class, 'index']); // Alias
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // 3. Manage Produk
    Route::get('/manage-produk', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products', [AdminProductController::class, 'index']); // Alias
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // 4. Manage Kategori
    Route::get('/manage-kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories', [AdminCategoryController::class, 'index']); // Alias
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');

    // 5. Manage Stock
    Route::get('/manage-stock', [AdminStockController::class, 'index'])->name('stock.index');
    Route::get('/stock', [AdminStockController::class, 'index']); // Alias
    Route::post('/stock/{id}/restock', [AdminStockController::class, 'restock'])->name('stock.restock');
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $products = AdminProductController::getDummyProducts();

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'Semua') {
            $catFilter = $request->category;
            $products = $products->filter(function ($p) use ($catFilter) {
                return $p->category_name === $catFilter || $p->display_category === $catFilter;
            });
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $products = $products->filter(function ($p) use ($search) {
                return str_contains(strtolower($p->name), $search)
                    || str_contains(strtolower($p->sku), $search)
                    || str_contains(strtolower($p->category_name), $search);
            });
        }

        $categories = AdminCategoryController::getDummyCategories()->where('is_active', true);

        // Ringkasan metrik inventaris
        $totalRegistered = $products->count();
        $totalRestockNeeded = $products->where('stock_status_label', 'Perlu Restock')->count();
        $totalCritical = $products->where('stock_status_label', 'Kritis')->count();

        return view('admin.stock.index', compact(
            'products',
            'categories',
            'totalRegistered',
            'totalRestockNeeded',
            'totalCritical'
        ));
    }

    public function restock(Request $request, $id)
    {
        $qty = (int) $request->input('quantity', 20);

        return back()->with(
            'success',
            "Stok produk berhasil ditambah {$qty} Pcs (Mode Dummy)."
        );
    }
}

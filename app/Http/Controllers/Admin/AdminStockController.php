<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter kategori (code atau nama display)
        if ($request->filled('category') && $request->category !== 'Semua') {
            $matchedKey = self::matchCategoryCode($request->category);
            $query->when($matchedKey, fn ($q) => $q->where('category', $matchedKey));
        }

        // Filter status inventaris
        if ($request->filled('stock_status') && $request->stock_status !== 'Semua') {
            match ($request->stock_status) {
                'Kritis' => $query->where('stock', '<=', 0),
                'Perlu Restock' => $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0),
                default => $query->whereColumn('stock', '>', 'min_stock'),
            };
        }

        // Filter pencarian (nama / kategori)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderByRaw('CASE WHEN stock <= 0 THEN 0 WHEN stock <= min_stock THEN 1 ELSE 2 END')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::active()->ordered()->get();

        // Ringkasan metrik inventaris (menghitung seluruh produk, bukan hasil filter)
        $totalRegistered = Product::count();
        $totalRestockNeeded = Product::whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0)->count();
        $totalCritical = Product::where('stock', '<=', 0)->count();

        return view('admin.stock.index', compact(
            'products',
            'categories',
            'totalRegistered',
            'totalRestockNeeded',
            'totalCritical'
        ));
    }

    /**
     * Tambah stok produk.
     */
    public function restock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ], [
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.min' => 'Jumlah stok minimal 1.',
        ]);

        $product = Product::findOrFail($id);
        $product->increment('stock', $validated['quantity']);

        // Stok baru positif -> produk otomatis tersedia kembali
        if ($product->fresh()->stock > 0 && ! $product->available) {
            $product->update(['available' => true]);
        }

        return back()->with(
            'success',
            "Stok '{$product->name}' berhasil ditambah {$validated['quantity']} Pcs. Sisa stok: {$product->fresh()->stock} Pcs."
        );
    }

    /**
     * Set stok manual (untuk koreksi hasil hitung atau barang hilang).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ], [
            'stock.required' => 'Jumlah stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'] ?? $product->min_stock,
            'available' => $validated['stock'] > 0 ? $product->available : false,
        ]);

        return back()->with('success', "Stok '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Cocokkan input kategori (code / nama) dengan kode kategori yang valid.
     */
    private static function matchCategoryCode(string $input): ?string
    {
        $needle = strtoupper(trim($input));

        $fromDb = Category::whereRaw('UPPER(code) = ?', [$needle])
            ->orWhereRaw('UPPER(name) = ?', [$needle])
            ->value('code');

        if ($fromDb) {
            return $fromDb;
        }

        foreach (Product::CATEGORIES as $code => $meta) {
            if (strtoupper($code) === $needle || strtoupper($meta['name']) === $needle) {
                return $code;
            }
        }

        return null;
    }
}
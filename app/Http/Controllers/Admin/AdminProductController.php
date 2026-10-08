<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    /**
     * Data dummy produk sebagai fallback jika database kosong
     */
    public static function getDummyProducts()
    {
        return collect([
            (object) [
                'id' => 1,
                'name' => 'Nugget Ayam Premium',
                'sku' => 'DC-001',
                'category_name' => 'Frozen',
                'display_category' => 'Frozen',
                'price' => 48000,
                'formatted_price' => 'Rp 48.000',
                'stock' => 45,
                'min_stock' => 10,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => 'products/chicken-katsu.webp',
                'image_url' => asset('images/products/chicken-katsu.webp'),
                'description' => 'Dibuat dari 100% daging ayam fillet segar pilihan tanpa bahan pengawet.',
                'updated_at' => now()->subHours(1),
            ],
            (object) [
                'id' => 2,
                'name' => 'Siomay Frozen Premium',
                'sku' => 'DC-002',
                'category_name' => 'Frozen',
                'display_category' => 'Frozen',
                'price' => 35000,
                'formatted_price' => 'Rp 35.000',
                'stock' => 8,
                'min_stock' => 15,
                'status_class' => 'badge-warning',
                'status_label' => 'Stok Rendah',
                'stock_status_class' => 'badge-warning',
                'stock_status_label' => 'Perlu Restock',
                'image_path' => 'products/siomay.webp',
                'image_url' => asset('images/products/siomay.webp'),
                'description' => 'Siomay ayam kukus lengkap dengan bumbu kacang gurih legit.',
                'updated_at' => now()->subHours(3),
            ],
            (object) [
                'id' => 3,
                'name' => 'Risol Mayo Smoked Beef',
                'sku' => 'DC-003',
                'category_name' => 'Cemilan',
                'display_category' => 'Cemilan',
                'price' => 25000,
                'formatted_price' => 'Rp 25.000',
                'stock' => 2,
                'min_stock' => 10,
                'status_class' => 'badge-danger',
                'status_label' => 'Kritis',
                'stock_status_class' => 'badge-danger',
                'stock_status_label' => 'Kritis',
                'image_path' => 'products/risol-mayo.webp',
                'image_url' => asset('images/products/risol-mayo.webp'),
                'description' => 'Isian smoked beef premium, keju cheddar gurih, dan mayones melimpah.',
                'updated_at' => now()->subHours(5),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'Semua') {
            $cat = strtoupper($request->category);
            // Cek apakah match langsung atau via display name
            $matchedKey = null;
            foreach (Product::CATEGORIES as $key => $meta) {
                if (strtoupper($key) === $cat || strtoupper($meta['name']) === $cat) {
                    $matchedKey = $key;
                    break;
                }
            }
            if ($matchedKey) {
                $query->where('category', $matchedKey);
            } else {
                $query->where('category', $request->category);
            }
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $totalProductsCount = Product::count();
        $products = $query->orderBy('id', 'desc')->paginate(8)->withQueryString();

        // Ambil daftar kategori dari Product::CATEGORIES
        $categories = collect(Product::CATEGORIES)->map(function ($cat, $key) {
            return (object) [
                'id' => $key,
                'code' => $key,
                'name' => $cat['name'],
                'key' => $key,
                'description' => $cat['description'] ?? '',
                'is_active' => true,
            ];
        });

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'totalProductsCount' => $totalProductsCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Normalisasi kategori
        $categoryKey = strtoupper($request->input('category_name'));
        foreach (Product::CATEGORIES as $key => $meta) {
            if (strtoupper($meta['name']) === $categoryKey || strtoupper($key) === $categoryKey) {
                $categoryKey = $key;
                break;
            }
        }

        Product::create([
            'name' => $validated['name'],
            'category' => $categoryKey,
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? '',
            'available' => true,
            'rating' => 5.0,
            'sold' => 0,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk baru '{$validated['name']}' berhasil ditambahkan ke katalog!");
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);

        $categoryKey = $product->category;
        if ($request->filled('category_name')) {
            $catInput = strtoupper($request->input('category_name'));
            foreach (Product::CATEGORIES as $key => $meta) {
                if (strtoupper($meta['name']) === $catInput || strtoupper($key) === $catInput) {
                    $categoryKey = $key;
                    break;
                }
            }
        }

        $product->update([
            'name' => $validated['name'],
            'category' => $categoryKey,
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? $product->description,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Data produk '{$product->name}' berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$name}' berhasil dihapus dari sistem.");
    }
}

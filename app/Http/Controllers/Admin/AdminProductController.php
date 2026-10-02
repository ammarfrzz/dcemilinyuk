<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminProductController extends Controller
{
    /**
     * Data dummy produk mandiri tanpa database
     */
    public static function getDummyProducts()
    {
        return collect([
            (object) [
                'id' => 1,
                'name' => 'Nugget Ayam Premium',
                'sku' => 'NUG-001',
                'category_name' => 'Frozen Food',
                'display_category' => 'Frozen Food',
                'price' => 48000,
                'formatted_price' => 'Rp 48.000',
                'stock' => 45,
                'min_stock' => 10,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => null,
                'description' => 'Dibuat dari 100% daging ayam fillet segar pilihan tanpa bahan pengawet.',
                'updated_at' => now()->subHours(1),
            ],
            (object) [
                'id' => 2,
                'name' => 'Siomay Frozen Premium',
                'sku' => 'SIO-002',
                'category_name' => 'Frozen Food',
                'display_category' => 'Frozen Food',
                'price' => 35000,
                'formatted_price' => 'Rp 35.000',
                'stock' => 8,
                'min_stock' => 15,
                'status_class' => 'badge-warning',
                'status_label' => 'Stok Rendah',
                'stock_status_class' => 'badge-warning',
                'stock_status_label' => 'Perlu Restock',
                'image_path' => null,
                'description' => 'Siomay ikan tenggiri asli lengkap dengan racikan saus bumbu kacang gurih legit.',
                'updated_at' => now()->subHours(3),
            ],
            (object) [
                'id' => 3,
                'name' => 'Risol Mayo Smoked Beef',
                'sku' => 'RIS-003',
                'category_name' => 'Makanan Ringan',
                'display_category' => 'Makanan Ringan',
                'price' => 25000,
                'formatted_price' => 'Rp 25.000',
                'stock' => 2,
                'min_stock' => 10,
                'status_class' => 'badge-danger',
                'status_label' => 'Kritis',
                'stock_status_class' => 'badge-danger',
                'stock_status_label' => 'Kritis',
                'image_path' => null,
                'description' => 'Isian smoked beef premium, keju cheddar gurih, dan mayones melimpah.',
                'updated_at' => now()->subHours(5),
            ],
            (object) [
                'id' => 4,
                'name' => 'Cireng Salju Bumbu Rujak',
                'sku' => 'CIR-004',
                'category_name' => 'Makanan Ringan',
                'display_category' => 'Makanan Ringan',
                'price' => 18000,
                'formatted_price' => 'Rp 18.000',
                'stock' => 60,
                'min_stock' => 15,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => null,
                'description' => 'Cireng kenyal gurih renyah di luar dengan cocolan sambal rujak gula merah pedas manis.',
                'updated_at' => now()->subDay(),
            ],
            (object) [
                'id' => 5,
                'name' => 'Makaroni Pedas Daun Jeruk',
                'sku' => 'MAK-005',
                'category_name' => 'Camilan Pedas',
                'display_category' => 'Camilan Pedas',
                'price' => 15000,
                'formatted_price' => 'Rp 15.000',
                'stock' => 6,
                'min_stock' => 10,
                'status_class' => 'badge-warning',
                'status_label' => 'Stok Rendah',
                'stock_status_class' => 'badge-warning',
                'stock_status_label' => 'Perlu Restock',
                'image_path' => null,
                'description' => 'Makaroni bantet garing renyah dipadukan bubuk cabai merah asli dan aroma daun jeruk.',
                'updated_at' => now()->subHours(12),
            ],
            (object) [
                'id' => 6,
                'name' => 'Basreng Pedas Nampol',
                'sku' => 'BAS-006',
                'category_name' => 'Camilan Pedas',
                'display_category' => 'Camilan Pedas',
                'price' => 16000,
                'formatted_price' => 'Rp 16.000',
                'stock' => 0,
                'min_stock' => 10,
                'status_class' => 'badge-danger',
                'status_label' => 'Habis',
                'stock_status_class' => 'badge-danger',
                'stock_status_label' => 'Kritis',
                'image_path' => null,
                'description' => 'Bakso goreng ikan tenggiri iris renyah pedas mantap level super.',
                'updated_at' => now()->subDays(2),
            ],
            (object) [
                'id' => 7,
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'KOP-007',
                'category_name' => 'Minuman Segar',
                'display_category' => 'Minuman Segar',
                'price' => 20000,
                'formatted_price' => 'Rp 20.000',
                'stock' => 35,
                'min_stock' => 10,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => null,
                'description' => 'Espresso house blend dipadukan fresh milk creamy dan sirup gula aren murni.',
                'updated_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 8,
                'name' => 'Es Teh Melati Jumbo',
                'sku' => 'TEH-008',
                'category_name' => 'Minuman Segar',
                'display_category' => 'Minuman Segar',
                'price' => 8000,
                'formatted_price' => 'Rp 8.000',
                'stock' => 80,
                'min_stock' => 20,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => null,
                'description' => 'Teh melati wangi seduhan tradisional disajikan dingin menyegarkan cup 22oz.',
                'updated_at' => now()->subHours(4),
            ],
            (object) [
                'id' => 9,
                'name' => 'Dimsum Ayam Jamur',
                'sku' => 'DIM-009',
                'category_name' => 'Frozen Food',
                'display_category' => 'Frozen Food',
                'price' => 32000,
                'formatted_price' => 'Rp 32.000',
                'stock' => 14,
                'min_stock' => 10,
                'status_class' => 'badge-success',
                'status_label' => 'Tersedia',
                'stock_status_class' => 'badge-success',
                'stock_status_label' => 'Aman',
                'image_path' => null,
                'description' => 'Dimsum isi daging ayam juicy cincang dan irisan jamur kuping lezat.',
                'updated_at' => now()->subDays(1),
            ],
            (object) [
                'id' => 10,
                'name' => 'Otak-Otak Ikan Tenggiri',
                'sku' => 'OTA-010',
                'category_name' => 'Makanan Ringan',
                'display_category' => 'Makanan Ringan',
                'price' => 22000,
                'formatted_price' => 'Rp 22.000',
                'stock' => 4,
                'min_stock' => 12,
                'status_class' => 'badge-warning',
                'status_label' => 'Stok Rendah',
                'stock_status_class' => 'badge-warning',
                'stock_status_label' => 'Perlu Restock',
                'image_path' => null,
                'description' => 'Otak-otak panggang khas aroma daun pisang dengan bumbu kacang pedas manis.',
                'updated_at' => now()->subHours(7),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $products = self::getDummyProducts();

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
        $totalProductsCount = $products->count();

        // Paginate data dummy
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 8;
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage)->values(),
            $totalProductsCount,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.products.index', [
            'products' => $paginatedProducts,
            'categories' => $categories,
            'totalProductsCount' => $totalProductsCount,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk baru '{$request->name}' berhasil ditambahkan (Mode Dummy)!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Data produk '{$request->name}' berhasil diperbarui (Mode Dummy)!");
    }

    public function destroy($id)
    {
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus (Mode Dummy).');
    }
}

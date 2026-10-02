<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    /**
     * Data dummy kategori mandiri tanpa database
     */
    public static function getDummyCategories()
    {
        return collect([
            (object) [
                'id' => 1,
                'name' => 'Makanan Ringan',
                'code' => 'CAT-001',
                'description' => 'Aneka gorengan gurih, keripik renyah, dan jajanan pasar favorit.',
                'is_active' => true,
            ],
            (object) [
                'id' => 2,
                'name' => 'Minuman Segar',
                'code' => 'CAT-002',
                'description' => 'Es teh nusantara, racikan kopi susu gula aren, dan minuman segar.',
                'is_active' => true,
            ],
            (object) [
                'id' => 3,
                'name' => 'Camilan Pedas',
                'code' => 'CAT-003',
                'description' => 'Sensasi jajanan pedas nampol daun jeruk dan cabai rawit melimpah.',
                'is_active' => true,
            ],
            (object) [
                'id' => 4,
                'name' => 'Frozen Food',
                'code' => 'CAT-004',
                'description' => 'Makanan beku siap goreng dan kukus praktis tahan lama.',
                'is_active' => true,
            ],
            (object) [
                'id' => 5,
                'name' => 'Kue & Pastry',
                'code' => 'CAT-005',
                'description' => 'Aneka cake lembut, bolu panggang, dan pastry manis.',
                'is_active' => false,
            ],
        ]);
    }

    public function index()
    {
        $categories = self::getDummyCategories();
        $totalCount = $categories->count();
        $activeCount = $categories->where('is_active', true)->count();

        return view('admin.categories.index', compact('categories', 'totalCount', 'activeCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$request->name}' berhasil ditambahkan (Mode Dummy).");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$request->name}' berhasil diperbarui (Mode Dummy).");
    }
}
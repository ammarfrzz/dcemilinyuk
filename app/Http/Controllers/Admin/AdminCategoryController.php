<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index()
    {
        // Pastikan kategori dasar selalu ada (dari metadata hardcoded) lalu ambil yang aktif.
        $this->ensureDefaultCategoriesExist();

        $categories = Category::withCount('products')->orderBy('sort_order')->orderBy('name')->get();

        $totalCount = $categories->count();
        $activeCount = $categories->where('is_active', true)->count();

        return view('admin.categories.index', compact('categories', 'totalCount', 'activeCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('categories', 'code')],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'code.regex' => 'Kode kategori hanya boleh huruf, angka, tanda hubung, atau underscore.',
            'code.unique' => 'Kode kategori sudah dipakai.',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'code' => $this->generateCode($validated['code'] ?? $validated['name']),
            'icon' => $validated['icon'] ?? 'fa-solid fa-basket-shopping',
            'description' => $validated['description'] ?? '',
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? (Category::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? $category->description,
            'icon' => $validated['icon'] ?? $category->icon,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;

        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$name}' tidak bisa dihapus karena masih dipakai oleh produk. Nonaktifkan kategori sebagai gantinya.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }

    /**
     * Buat kategori dasar dari metadata hardcoded bila belum ada di database.
     */
    private function ensureDefaultCategoriesExist(): void
    {
        foreach (Product::CATEGORIES as $code => $meta) {
            Category::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $meta['name'],
                    'slug' => Str::slug($meta['name']),
                    'icon' => $meta['icon'],
                    'image_path' => $meta['image'],
                    'description' => $meta['description'],
                    'is_active' => true,
                    'sort_order' => array_search($code, array_keys(Product::CATEGORIES), true),
                ]
            );
        }
    }

    /**
     * Kode kategori unik dari nama bila tidak diberikan.
     */
    private function generateCode(string $input): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', trim($input)));
        $base = trim($base, '_') ?: 'KATEGORI';
        $code = substr($base, 0, 50);
        $suffix = 1;

        while (Category::where('code', $code)->exists()) {
            $code = substr($base, 0, 45) . '_' . ++$suffix;
        }

        return $code;
    }
}
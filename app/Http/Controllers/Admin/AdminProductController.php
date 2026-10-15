<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'Semua') {
            $matchedKey = self::matchCategoryCode($request->category);
            $query->when($matchedKey, fn ($q) => $q->where('category', $matchedKey));
        }

        // Filter ketersediaan
        if ($request->filled('availability') && $request->availability !== 'Semua') {
            $query->where('available', $request->availability === 'Tersedia');
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
        $products = $query->orderByDesc('id')->paginate(8)->withQueryString();

        $categories = Category::active()->ordered()->get();

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
            'price' => 'required|numeric|min:0|max:99999999',
            'description' => 'nullable|string|max:1000',
            'stock' => 'nullable|integer|min:0|max:100000',
            'min_stock' => 'nullable|integer|min:0|max:100000',
            'badge' => 'nullable|in:best_seller,baru,pedas',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'whatsapp_order_link' => 'nullable|url|max:255',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'category_name.required' => 'Kategori wajib dipilih.',
            'price.required' => 'Harga wajib diisi.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ], [
            'category_name' => 'kategori',
        ]);

        // Normalisasi kategori (terima code maupun nama display)
        $categoryKey = self::matchCategoryCode($validated['category_name'])
            ?? strtoupper($validated['category_name']);

        $stock = $validated['stock'] ?? 45;

        $product = Product::create([
            'name' => $validated['name'],
            'category' => $categoryKey,
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? '',
            'badge' => $validated['badge'] ?? null,
            'stock' => $stock,
            'min_stock' => $validated['min_stock'] ?? 10,
            'available' => $stock > 0,
            'rating' => 5.0,
            'sold' => 0,
            'image_path' => $this->handleImageUpload($request),
            'whatsapp_order_link' => $validated['whatsapp_order_link'] ?? null,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk baru '{$product->name}' berhasil ditambahkan ke katalog!");
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:99999999',
            'description' => 'nullable|string|max:1000',
            'stock' => 'nullable|integer|min:0|max:100000',
            'min_stock' => 'nullable|integer|min:0|max:100000',
            'badge' => 'nullable|in:best_seller,baru,pedas',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'whatsapp_order_link' => 'nullable|url|max:255',
            'remove_image' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($id);

        $data = [
            'name' => $validated['name'],
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? $product->description,
            'badge' => $validated['badge'] ?? null,
            'min_stock' => $validated['min_stock'] ?? $product->min_stock,
            'whatsapp_order_link' => $validated['whatsapp_order_link'] ?? null,
        ];

        if ($request->filled('category_name')) {
            $data['category'] = self::matchCategoryCode($validated['category_name'])
                ?? strtoupper($validated['category_name']);
        }

        if ($request->has('stock')) {
            $data['stock'] = $validated['stock'];
            $data['available'] = $validated['stock'] > 0;
        }

        // Upload gambar baru (hapus gambar lama di storage jika ada)
        if ($request->hasFile('image')) {
            $this->deleteStoredImage($product->image_path);
            $data['image_path'] = $this->handleImageUpload($request);
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredImage($product->image_path);
            $data['image_path'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Data produk '{$product->name}' berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Order lama tetap referring ke produk, jadi jangan cascade hapus diam-diam
        if ($product->orders()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', "Produk '{$product->name}' tidak bisa dihapus karena sudah memiliki riwayat pesanan. Nonaktifkan produk sebagai gantinya.");
        }

        $this->deleteStoredImage($product->image_path);

        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$name}' berhasil dihapus dari sistem.");
    }

    /**
     * Simpan gambar produk ke storage publik dan kembalikan path relatifnya.
     */
    private function handleImageUpload(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . Str::random(6)
            . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('products', $filename, 'public');

        return 'storage/' . $path;
    }

    /**
     * Hapus file gambar yang tersimpan di storage publik saja (bukan gambar bawaan repo).
     */
    private function deleteStoredImage(?string $imagePath): void
    {
        if (! $imagePath || ! str_starts_with($imagePath, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('storage/', '', $imagePath));
    }

    /**
     * Cocokkan input kategori (code / nama) dengan kode kategori yang valid.
     * Cek database dulu, lalu fallback ke metadata hardcoded.
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
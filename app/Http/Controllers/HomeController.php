<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categoryCode = $request->query('kategori');

        // Metadata kategori untuk tab filter & section Categories.
        // Prioritaskan data DB, fallback ke instance Category yang belum disimpan
        // supaya accessor (image_url, dll.) tetap bekerja.
        $categories = Category::active()->ordered()->get();

        if ($categories->isEmpty()) {
            $categories = collect(Product::CATEGORIES)
                ->map(function ($meta, $code) {
                    $category = new Category([
                        'name' => $meta['name'],
                        'icon' => $meta['icon'],
                        'image_path' => $meta['image'],
                        'description' => $meta['description'],
                        'is_active' => true,
                    ]);

                    // code & slug tidak ada di $fillable, jadi diisi eksplisit
                    $category->forceFill([
                        'code' => $code,
                        'slug' => Str::slug($meta['name']),
                    ]);

                    return $category;
                })
                ->values();
        }

        // Validasi kategori dari query string (jika tidak ada / tidak valid, tampilkan semua).
        $selectedCategory = $categoryCode && $categories->contains('code', strtoupper($categoryCode))
            ? strtoupper($categoryCode)
            : null;

        return view('home', [
            // Grid produk dengan tab filter kategori (mirip Products.tsx)
            'products' => Product::available()
                ->with('categoryInfo:id,name,code,icon')
                ->category($selectedCategory)
                ->orderBy('category')
                ->orderBy('name')
                ->get(),

            // Best seller untuk section Featured (8 teratas berdasarkan sold)
            'bestSellers' => Product::bestSellers(),

            // Metadata kategori untuk tab filter & section Categories
            'categories' => $categories,

            // Produk MINUMAN untuk section SpecialtyDrinks (menu signature)
            'minumanProducts' => Product::available()
                ->where('category', Product::CATEGORY_MINUMAN)
                ->orderByDesc('sold')
                ->get(),

            'selectedCategory' => $selectedCategory,
            'storeSettings' => [
                'brand_name' => Setting::get('brand_name', config('dcemilinyuk.brand')),
                'brand_tagline' => Setting::get('brand_tagline'),
                'wa_number' => Setting::get('wa_number', config('dcemilinyuk.wa_number')),
                'support_email' => Setting::get('support_email'),
                'support_address' => Setting::get('support_address'),
            ],
        ]);
    }
}
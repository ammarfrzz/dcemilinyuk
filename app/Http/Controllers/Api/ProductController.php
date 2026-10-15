<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:50'],
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,best_seller,name'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:60'],
        ], [
            'sort.in' => 'Urutan tidak valid. Pilihan: newest, price_asc, price_desc, best_seller, name.',
            'per_page.max' => 'Maksimal 60 data per halaman.',
        ]);

        $query = Product::available()->with('categoryInfo:id,name,code,icon');

        $query->category($validated['category'] ?? null)
            ->search($validated['search'] ?? null);

        match ($validated['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'best_seller' => $query->orderByDesc('sold'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('id'),
        };

        $products = $query->paginate($validated['per_page'] ?? 12);

        return response()->json([
            'data' => array_map(fn (Product $product) => $this->transform($product), $products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::available()
            ->with('categoryInfo:id,name,code,icon')
            ->findOrFail($id);

        return response()->json([
            'data' => $this->transform($product),
        ]);
    }

    /**
     * Bentuk respons produk yang sama untuk index & show.
     *
     * @return array<string, mixed>
     */
    private function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'category' => $product->display_category,
            'category_code' => $product->category,
            'description' => $product->description,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'rating' => (float) $product->rating,
            'sold' => $product->sold,
            'stock' => $product->stock,
            'badge' => $product->badge_label,
            'image_url' => $product->image_url,
            'whatsapp_order_url' => $product->whatsapp_order_url,
        ];
    }
}
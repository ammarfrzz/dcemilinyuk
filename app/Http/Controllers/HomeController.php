<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            // Grid produk dengan tab filter kategori (mirip Products.tsx)
            'products' => Product::available()->orderBy('category')->orderBy('name')->get(),

            // Best seller untuk section Featured (8 teratas berdasarkan sold)
            'bestSellers' => Product::bestSellers(),

            // Metadata kategori untuk tab filter & section Categories
            'categories' => Product::CATEGORIES,

            // Produk MINUMAN untuk section SpecialtyDrinks (menu signature)
            'minumanProducts' => Product::available()
                ->where('category', Product::CATEGORY_MINUMAN)
                ->orderByDesc('sold')
                ->get(),
        ]);
    }
}

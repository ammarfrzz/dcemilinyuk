<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::active()->ordered()->get(['id', 'name', 'slug', 'code', 'icon', 'description']);

        return response()->json([
            'data' => $categories,
            'meta' => ['total' => $categories->count()],
        ]);
    }
}
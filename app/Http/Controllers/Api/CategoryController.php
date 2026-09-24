<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController
{
    public function index()
    {
        return response()->json([
            'categories' => Category::all(),
        ]);
    }
}

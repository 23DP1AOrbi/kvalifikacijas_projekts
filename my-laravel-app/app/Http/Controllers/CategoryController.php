<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return Category::withCount('designs')->get();
    }

    public function store(Request $request) {
        $cat = Category::create($request->validate(['name' => 'required|string']));
        return response()->json($cat);
    }

    public function destroy(Category $category)
    {
        // 1. Remove all links to designs in the pivot table first
        // This ensures designs aren't affected by the deletion
        $category->designs()->detach();

        // 2. Now delete the category itself
        $category->delete();

        return response()->json(['message' => 'Category removed successfully']);
    }
}
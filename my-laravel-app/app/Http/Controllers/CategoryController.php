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
        // removes the category from all the designs
        $category->designs()->detach();

        $category->delete();

        return response()->json(['message' => 'Category removed successfully']);
    }
}
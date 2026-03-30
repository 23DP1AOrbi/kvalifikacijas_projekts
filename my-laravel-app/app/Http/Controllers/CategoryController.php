<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return Category::all();
    }

    public function store(Request $request) {
        $cat = Category::create($request->validate(['name' => 'required|string']));
        return response()->json($cat);
    }

    public function destroy(Category $category) {
        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
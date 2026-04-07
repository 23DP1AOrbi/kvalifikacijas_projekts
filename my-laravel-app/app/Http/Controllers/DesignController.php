<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    public function index()
    {
        // 1. Eager load categories using with('categories')
        // 2. We use get() instead of all() to allow for the 'with' chain
        $designs = Design::with('categories')->get()->map(function ($design) {
            
            // Ensure the file URL is accessible
            // Note: If your DB stores 'designs/filename.svg', 
            // this results in 'storage/designs/filename.svg'
            $design->file_url = asset('storage/' . $design->file_url);
            
            return $design;
        });

        return response()->json($designs);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|file|mimes:svg',
            'category_ids' => 'array'
        ]);

        // Store SVG in storage/app/public/designs
        $path = $request->file('image')->store('designs', 'public');

        $design = Design::create([
            'name' => $validated['name'],
            'file_url' => $path,
        ]);

        // Return the design with a full URL
        $design->file_url = asset('storage/' . $design->file_url);

        if ($request->has('category_ids')) {
            $design->categories()->sync($request->category_ids);
        }

        return response()->json($design->load('categories'), 201);
    }

    public function show($id)
    {
        $design = Design::with('categories')->findOrFail($id);
        $design->file_url = asset('storage/' . $design->file_url);

        return response()->json($design);
    }

    public function destroy($id)
    {
        $design = Design::findOrFail($id);

        // 1. Delete the physical file from storage
        if (Storage::disk('public')->exists($design->file_url)) {
            Storage::disk('public')->delete($design->file_url);
        }

        // 2. Delete the database record (this also removes pivot table links)
        $design->delete();

        return response()->json(['message' => 'Design deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user(); 

        $design = Design::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $design->update($data);

        return response()->json($design);
    }

    public function syncCategories(Request $request, Design $design)
    {
        // Validate that category_ids is an array of existing IDs
        $data = $request->validate([
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        // Use sync() to add/remove associations in the pivot table automatically
        $design->categories()->sync($data['category_ids'] ?? []);

        return response()->json(['message' => 'Categories synced successfully']);
    }
}
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
}
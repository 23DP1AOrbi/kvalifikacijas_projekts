<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::all()->map(function ($design) {
            // Ensure the file URL is accessible
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
        ]);

        // Store SVG in storage/app/public/designs
        $path = $request->file('image')->store('designs', 'public');

        $design = Design::create([
            'name' => $validated['name'],
            'file_url' => $path,
        ]);

        // Return the design with a full URL
        $design->file_url = asset('storage/' . $design->file_url);

        return response()->json($design, 201);
    }

    public function show($id)
    {
        $design = Design::findOrFail($id);
        $design->file_url = asset('storage/' . $design->file_url);

        return response()->json($design);
    }
}
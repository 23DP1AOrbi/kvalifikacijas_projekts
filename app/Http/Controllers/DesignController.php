<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DesignController extends Controller
{
    public function index()
    {
        // GETS all designs with their categories
        $designs = Design::with('categories')->get()->map(function ($design) {
            
            // creates full file path for designs
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
            'category_ids' => 'array',
            'is_color' => 'required|boolean'
        ]);

        // uploads svg in specific folder & saves the path for retrieving it
        $path = $request->file('image')->store('designs', 'public');

        $design = Design::create([
            'name' => $validated['name'],
            'file_url' => $path,
            'is_color' => $validated['is_color'],
        ]);

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

        if (Storage::disk('public')->exists($design->file_url)) {
            Storage::disk('public')->delete($design->file_url);
        }

        $design->delete();

        return response()->json(['message' => 'Design deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user(); 

        $design = Design::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_color' => 'sometimes|boolean',
        ]);

        $design->update($data);

        return response()->json($design);
    }

    public function syncCategories(Request $request, Design $design)
    {
        $data = $request->validate([
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        // syncs design with the new categories or returns a blank array
        $design->categories()->sync($data['category_ids'] ?? []);

        return response()->json(['message' => 'Categories synced successfully']);
    }

    public function getStats(Request $request)
    {
        // only admin can access
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalDesigns = \App\Models\Design::count();
        $totalCategories = \App\Models\Category::count();
        
        // calculates the average usage of categories per design
        $pivotCount = DB::table('category_design')->count();
        $avgCategories = $totalDesigns > 0 ? round($pivotCount / $totalDesigns, 1) : 0;

        // shows the yop 5 most saved designs as user projects
        $topDesigns = \App\Models\Design::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->take(5)
            ->get(['name', 'projects_count']);

        return response()->json([
            'total_designs' => $totalDesigns,
            'total_categories' => $totalCategories,
            'avg_categories' => $avgCategories,
            'top_designs' => $topDesigns
        ]);
    }
}
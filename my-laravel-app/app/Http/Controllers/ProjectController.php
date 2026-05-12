<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth; 

class ProjectController extends Controller
{
    public function index()
    {
        // gets projects that belong to the user
        $projects = Project::where('user_id', Auth::id())->latest()->get();
        return response()->json($projects);
    }

    public function show($id)
    {
        return Project::where('user_id', Auth::id())->findOrFail($id);
    }

    public function store(Request $request)
    {
        // make sure the user is loggen in
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'design_id' => 'required|exists:designs,id',
            'name'      => 'required|string|max:255',
            'color_data' => 'nullable|array',
        ]);

        $project = Project::create([
            'user_id'   => Auth::id(), 
            'design_id' => $validated['design_id'],
            'name'      => $validated['name'],
            'color_data' => $validated['color_data'],
        ]);

        return response()->json($project);
    }

    public function destroy(Project $project) {
        if ($project->user_id !== Auth::id()) abort(403);
        $project->delete();
        return response()->noContent();
    }

    public function update(Request $request, $id)
    {
        $project = Project::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'design_id' => 'required|exists:designs,id',
            'name' => 'required|string|max:255',
            'color_data' => 'nullable|array',
        ]);

        $project->update($validated);

        return response()->json($project);
    }
}
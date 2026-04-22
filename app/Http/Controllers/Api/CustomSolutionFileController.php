<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomSolutionFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CustomSolutionFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $files = CustomSolutionFile::with('customSolution')->get();
        return response()->json($files);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'custom_solution_id' => 'required|exists:custom_solutions,id',
            'file_path' => 'required|string|max:255',
        ]);

        $file = CustomSolutionFile::create($validated);
        return response()->json($file, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $file = CustomSolutionFile::with('customSolution')->findOrFail($id);
        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $file = CustomSolutionFile::findOrFail($id);

        $validated = $request->validate([
            'custom_solution_id' => 'sometimes|exists:custom_solutions,id',
            'file_path' => 'sometimes|string|max:255',
        ]);

        $file->update($validated);
        return response()->json($file);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $file = CustomSolutionFile::findOrFail($id);

        // Eliminar el archivo del storage
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return response()->json(['message' => 'File deleted successfully']);
    }
}
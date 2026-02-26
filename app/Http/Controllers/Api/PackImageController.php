<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackImageFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $images = PackImageFile::with('pack')->get();
        return response()->json($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'packs_id' => 'required|exists:packs,id',
            'is_important' => 'nullable|boolean',
        ]);

        $image = PackImageFile::create($validated);
        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $image = PackImageFile::with('pack')->findOrFail($id);
        return response()->json($image);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $image = PackImageFile::findOrFail($id);

        $validated = $request->validate([
            'packs_id' => 'sometimes|exists:packs,id',
            'is_important' => 'nullable|boolean',
        ]);

        $image->update($validated);
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $image = PackImageFile::findOrFail($id);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}

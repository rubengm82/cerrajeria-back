<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackImageFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
            'pack_id' => 'required|exists:packs,id',
            'image' => 'required|image|max:2048',
            'is_important' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('packs', 'public');

        $image = PackImageFile::create([
            'packs_id' => $validated['pack_id'],
            'path' => $path,
            'is_important' => $request->boolean('is_important'),
        ]);

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
            'pack_id' => 'sometimes|exists:packs,id',
            'is_important' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if (isset($validated['pack_id'])) {
            $validated['packs_id'] = $validated['pack_id'];
            unset($validated['pack_id']);
        }

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
            $validated['path'] = $request->file('image')->store('packs', 'public');
        }

        $image->update($validated);
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $image = PackImageFile::findOrFail($id);
        
        // Eliminar el archivo del storage
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}

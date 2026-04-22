<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImageFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $images = ProductImageFile::with('product')->get();
        return response()->json($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|max:2048',
            'is_important' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('products', 'public');

        $image = ProductImageFile::create([
            'product_id' => $validated['product_id'],
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
        $image = ProductImageFile::with('product')->findOrFail($id);
        return response()->json($image);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $image = ProductImageFile::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'is_important' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
            $validated['path'] = $request->file('image')->store('products', 'public');
        }

        $image->update($validated);
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $image = ProductImageFile::findOrFail($id);

        // Eliminar el archivo del storage
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}

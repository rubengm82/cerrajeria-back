<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImageFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
            'is_important' => 'nullable|boolean',
        ]);

        $image = ProductImageFile::create($validated);
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
        ]);

        $image->update($validated);
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $image = ProductImageFile::findOrFail($id);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductFeatureType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductFeatureTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $types = ProductFeatureType::with('features')->get();
        return response()->json($types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $type = ProductFeatureType::create($validated);
        return response()->json($type, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $type = ProductFeatureType::with('features')->findOrFail($id);
        return response()->json($type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $type = ProductFeatureType::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
        ]);

        $type->update($validated);
        return response()->json($type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $type = ProductFeatureType::findOrFail($id);
        $type->delete();
        return response()->json(['message' => 'Feature type deleted successfully']);
    }
}

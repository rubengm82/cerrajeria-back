<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $features = Feature::with(['type', 'products'])->get();
        return response()->json($features);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|exists:product_feature_types,id',
            'valor' => 'required|integer',
        ]);

        $feature = Feature::create($validated);
        return response()->json($feature, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $feature = Feature::with(['type', 'products'])->findOrFail($id);
        return response()->json($feature);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $feature = Feature::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|exists:product_feature_types,id',
            'valor' => 'sometimes|integer',
        ]);

        $feature->update($validated);
        return response()->json($feature);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $feature = Feature::findOrFail($id);
        $feature->delete();
        return response()->json(['message' => 'Feature deleted successfully']);
    }
}

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
        $features = Feature::with(['type'])->get();
        return response()->json($features);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:feature_types,id',
            'value' => 'required|string|max:255',
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
            'type_id' => 'sometimes|exists:feature_types,id',
            'value' => 'sometimes|string|max:255',
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

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $features = Feature::onlyTrashed()->with(['type'])->get();
        return response()->json($features);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $feature = Feature::onlyTrashed()->findOrFail($id);
        $feature->restore();
        return response()->json(['message' => 'Feature restored successfully', 'feature' => $feature]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $feature = Feature::onlyTrashed()->findOrFail($id);
        $feature->forceDelete();
        return response()->json(['message' => 'Feature permanently deleted']);
    }
}

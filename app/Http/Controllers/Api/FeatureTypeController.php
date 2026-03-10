<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class FeatureTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $types = FeatureType::with('features')->get();
        return response()->json($types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('feature_types')->where(function ($query) use ($request) { return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]); })],
        ]);

        $type = FeatureType::create($validated);
        return response()->json($type, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $type = FeatureType::with('features')->findOrFail($id);
        return response()->json($type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $type = FeatureType::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('feature_types')->where(function ($query) use ($request) { return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]); })->ignore($id)],
        ]);

        $type->update($validated);
        return response()->json($type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $type = FeatureType::findOrFail($id);
        $type->delete();
        return response()->json(['message' => 'Feature type deleted successfully']);
    }
}

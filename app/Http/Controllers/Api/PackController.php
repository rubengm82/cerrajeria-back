<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $packs = Pack::with(['products', 'images'])->get();
        return response()->json($packs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $pack = Pack::create($validated);
        return response()->json($pack, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $pack = Pack::with(['products', 'images'])->findOrFail($id);
        return response()->json($pack);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $pack = Pack::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'total_price' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $pack->update($validated);
        return response()->json($pack);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $pack = Pack::findOrFail($id);
        $pack->delete();
        return response()->json(['message' => 'Pack deleted successfully']);
    }
}

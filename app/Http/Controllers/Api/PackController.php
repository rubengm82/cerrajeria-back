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

            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $pack = Pack::create(['name' => $validated['name'], 'total_price' => $validated['total_price'], 'description' => $validated['description'] ?? null]);

        // Si se envian productos se añaden en los productos del pack
        if (!empty($validated['product_ids'])) {
            $pack->products()->attach($validated['product_ids']);
        }
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

            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $pack->update([
            'name' => $validated['name'] ?? $pack->name,
            'total_price' => $validated['total_price'] ?? $pack->total_price,
            'description' => $validated['description'] ?? $pack->description,
        ]);

        // Se buscan los productos que pertenecen al pack que no se han enviado y se eliminan y se crean los nuevos
        if (array_key_exists('product_ids', $validated)) {
            $pack->products()->sync($validated['product_ids']);
        }
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

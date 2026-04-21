<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'images', 'features.type'])->get();

        return response()->json($products);
    }

    /**
     * Display a listing of the resource with trashed.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $products = Product::withTrashed()->with(['category', 'images', 'features.type'])->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_stock_break' => 'nullable|boolean',
            'code' => 'nullable|string|unique:products,code|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'required|exists:categories,id',
            'is_installable' => 'nullable|boolean',
            'is_important_to_show' => 'nullable|boolean',
            'price_keys' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'feature_ids' => 'nullable|array',
            'feature_ids.*' => 'exists:features,id',
        ]);

        $product = Product::create($validated);

        if (! empty($validated['feature_ids'])) {
            $product->features()->attach($validated['feature_ids']);
        }

        return response()->json($product->load('features.type'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'images', 'features.type', 'packs', 'orders'])->findOrFail($id);

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'is_stock_break' => 'nullable|boolean',
            'code' => 'nullable|string|max:100|unique:products,code,'.$product->id,
            'discount' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'sometimes|exists:categories,id',
            'is_installable' => 'nullable|boolean',
            'is_important_to_show' => 'nullable|boolean',
            'price_keys' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'feature_ids' => 'nullable|array',
            'feature_ids.*' => 'exists:features,id',
        ]);

        $product->update($validated);

        if (isset($validated['feature_ids'])) {
            $product->features()->sync($validated['feature_ids']);
        }

        return response()->json($product->load('features.type'));
    }

    public function getImportantProducts(): JsonResponse
    {
        $importantProducts = Product::where('is_important_to_show', true)
            ->where('is_active', true)
            ->with(['category', 'images', 'features.type'])
            ->get();

        return response()->json($importantProducts);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Obtener los packs asociados a este producto
        $packs = $product->packs()->get();

        $product->delete();

        // Desactivar packs que queden con 0 productos activos
        foreach ($packs as $pack) {
            $activeProductsCount = $pack->products()->count();
            if ($activeProductsCount === 0 && ! $pack->trashed()) {
                $pack->delete();
            }
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $products = Product::onlyTrashed()->with(['category', 'images', 'features.type'])->get();

        return response()->json($products);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json(['message' => 'Product restored successfully', 'product' => $product->load('features.type')]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        return response()->json(['message' => 'Product permanently deleted']);
    }
}

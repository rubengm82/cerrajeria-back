<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    /**
     * Display a listing of all categories including soft deleted.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $categories = Category::withTrashed()->with('products')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->where(function ($query) use ($request) { return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]); })],
            'is_important_to_show' => 'required|boolean',
            'image' => 'required|image|max:2048',
        ]);
        // Se sube la imagen
        $image = $request->file('image')->store('categories', 'public');

        $category = Category::create(['name' => $validated['name'], 'is_important_to_show' => $validated['is_important_to_show'], 'image' => $image]);
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json($category);
    }

    public function getImportantCategories(): JsonResponse
    {
        $importantCategories = Category::where("is_important_to_show", true)->with("products")->get();
        return response()->json($importantCategories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('categories')->where(function ($query) use ($request) { return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]); })->ignore($id)],
            'is_important_to_show' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Se borrar imagen anterior si existe
        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            // Se guarda la nueva imagen
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }
        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $categories = Category::onlyTrashed()->get();
        return response()->json($categories);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return response()->json(['message' => 'Category restored successfully', 'category' => $category]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        
        // Delete image if exists
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->forceDelete();
        return response()->json(['message' => 'Category permanently deleted']);
    }
}

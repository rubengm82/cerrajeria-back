<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomSolution;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CustomSolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customSolutions = CustomSolution::all();
        return response()->json($customSolutions);
    }

    /**
     * Display a listing of the resource with trashed.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $customSolutions = CustomSolution::withTrashed()->get();
        return response()->json($customSolutions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'required|string',
            'status' => 'nullable|in:' . CustomSolution::STATUS_NEW,
            'images' => 'nullable|array|max:3',
            'images.*' => 'file|mimes:jpg,jpeg,png,bmp,gif,svg,webp,pdf,doc,docx,odt,xls,xlsx|max:2048',
        ]);

        $customSolution = CustomSolution::create([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'description' => $validated['description'],
            'status' => $validated['status'] ?? CustomSolution::STATUS_NEW,
        ]);

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('custom-solutions', 'public');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uploadedAt = now()->format('Ymd_His');
            $displayName = Str::slug($originalName) . '_' . $uploadedAt . ($extension ? '.' . $extension : '');

            $customSolution->files()->create([
                'file_path' => $path,
                'original_name' => $displayName,
            ]);
        }

        return response()->json($customSolution->load('files'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $customSolution = CustomSolution::with(['files'])->findOrFail($id);
        return response()->json($customSolution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $customSolution = CustomSolution::findOrFail($id);

        $validated = $request->validate([
            'email' => 'sometimes|string|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:' . implode(',', CustomSolution::STATUSES),
        ]);

        $customSolution->update($validated);
        return response()->json($customSolution);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $customSolution = CustomSolution::findOrFail($id);
        $customSolution->delete();
        return response()->json(['message' => 'Custom solution deleted successfully']);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $customSolutions = CustomSolution::onlyTrashed()->get();
        return response()->json($customSolutions);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $customSolution = CustomSolution::onlyTrashed()->findOrFail($id);
        $customSolution->restore();
        return response()->json(['message' => 'Custom solution restored successfully', 'customSolution' => $customSolution]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $customSolution = CustomSolution::withTrashed()->with('files')->findOrFail($id);

        // Eliminar archivos físicos de las imágenes asociadas
        foreach ($customSolution->files as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $customSolution->forceDelete();
        return response()->json(['message' => 'Custom solution permanently deleted']);
    }
}

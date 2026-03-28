<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomSolution;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomSolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customSolutions = CustomSolution::with(['user', 'files'])->get();
        return response()->json($customSolutions);
    }

    /**
     * Display a listing of the resource with trashed.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $customSolutions = CustomSolution::withTrashed()->with(['user', 'files'])->get();
        return response()->json($customSolutions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'required|string',
            'status' => 'nullable|in:pending,closed',
        ]);

        // Si el usuario está autenticado, guardar su ID automáticamente
        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        } else {
            // Si no está autenticado, dejar user_id como null
            unset($validated['user_id']);
        }

        $customSolution = CustomSolution::create($validated);
        return response()->json($customSolution, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $customSolution = CustomSolution::with(['user', 'files'])->findOrFail($id);
        return response()->json($customSolution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $customSolution = CustomSolution::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'email' => 'sometimes|string|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,closed',
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
        $customSolutions = CustomSolution::onlyTrashed()->with(['user', 'files'])->get();
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
        $customSolution = CustomSolution::onlyTrashed()->findOrFail($id);
        $customSolution->forceDelete();
        return response()->json(['message' => 'Custom solution permanently deleted']);
    }
}

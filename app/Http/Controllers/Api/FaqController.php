<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of active FAQs.
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::whereNull('deleted_at')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($faqs);
    }

    /**
     * Display a listing of the resource with trashed.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $faqs = Faq::withTrashed()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($faqs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $faq = Faq::create($validated);

        return response()->json($faq, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        return response()->json($faq);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string',
        ]);

        $faq->update($validated);

        return response()->json($faq);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json(['message' => 'FAQ deleted successfully']);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $faqs = Faq::onlyTrashed()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($faqs);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->restore();

        return response()->json(['message' => 'FAQ restored successfully', 'faq' => $faq]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->forceDelete();

        return response()->json(['message' => 'FAQ permanently deleted']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        // Si el usuario es admin, mostrar todas las órdenes
        if ($user->role === 'admin' || $user->role === 1) {
            $orders = Order::with(['user', 'products'])->get();
        } else {
            // Si es usuario normal, mostrar solo sus órdenes
            $orders = Order::with(['user', 'products'])
                          ->where('user_id', $user->id)
                          ->get();
        }

        return response()->json($orders);
    }

    /**
     * Display a listing of the resource including soft deleted.
     */
    public function indexWithTrashed(): JsonResponse
    {
        $user = auth()->user();

        // Si el usuario es admin, mostrar todas las órdenes incluyendo eliminadas
        if ($user->role === 'admin' || $user->role === 1) {
            $orders = Order::withTrashed()->with(['user', 'products'])->get();
        } else {
            // Si es usuario normal, mostrar solo sus órdenes incluyendo eliminadas
            $orders = Order::withTrashed()->with(['user', 'products'])
                          ->where('user_id', $user->id)
                          ->get();
        }

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'installation_address' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|in:paypal,card,bizum',
            'status' => 'nullable|in:in_cart,pending,shipped,installation_confirmed',
        ]);

        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'products'])->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'installation_address' => 'sometimes|string|max:255',
            'shipping_address' => 'sometimes|string|max:255',
            'payment_method' => 'sometimes|in:paypal,card,bizum',
            'status' => 'sometimes|in:in_cart,pending,shipped,installation_confirmed',
            'shipped_at' => 'nullable|date',
        ]);

        $order->update($validated);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $orders = Order::onlyTrashed()->with(['user', 'products'])->get();
        return response()->json($orders);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();
        return response()->json(['message' => 'Order restored successfully', 'order' => $order]);
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();
        return response()->json(['message' => 'Order permanently deleted']);
    }
}

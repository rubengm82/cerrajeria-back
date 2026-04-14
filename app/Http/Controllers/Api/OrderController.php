<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
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
            $orders = Order::with(['user', 'products', 'packs'])->get();
        } else {
            // Si es usuario normal, mostrar solo sus órdenes
            $orders = Order::with(['user', 'products', 'packs'])
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
            $orders = Order::withTrashed()->with(['user', 'products', 'packs'])->get();
        } else {
            // Si es usuario normal, mostrar solo sus órdenes incluyendo eliminadas
            $orders = Order::withTrashed()->with(['user', 'products', 'packs'])
                          ->where('user_id', $user->id)
                          ->get();
        }

        return response()->json($orders);
    }

    /**
     * Display the authenticated user's current cart.
     */
    public function cart(): JsonResponse
    {
        $user = auth()->user();

        $order = Order::with(['products.category', 'products.images', 'packs.images', 'packs.products'])
            ->where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->first();

        return response()->json($order);
    }

    /**
     * Add a product to the authenticated user's current cart.
     */
    public function addProductToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $product = Product::findOrFail($validated['product_id']);

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'in_cart',
                'installation_address' => $user->address ?? 'Pendent de confirmar',
                'shipping_address' => $user->address ?? 'Pendent de confirmar',
                'payment_method' => 'bizum',
            ]);
        }

        $currentProduct = $order->products()
            ->where('products.id', $validated['product_id'])
            ->first();

        if ($currentProduct) {
            $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

            return response()->json([
                'added' => false,
                'message' => 'Aquest producte ja és al carret.',
                'order' => $order,
            ]);
        }

        if ($validated['quantity'] > $product->stock) {
            return response()->json([
                'message' => "Només hi ha {$product->stock} unitats disponibles.",
            ], 422);
        }

        $order->products()->attach($validated['product_id'], [
            'quantity' => $validated['quantity'],
        ]);

        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json([
            'added' => true,
            'order' => $order,
        ]);
    }

    /**
     * Add a pack to the authenticated user's current cart.
     */
    public function addPackToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pack_id' => 'required|exists:packs,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $pack = Pack::with('products')->findOrFail($validated['pack_id']);
        $availableStock = $pack->products->min('stock') ?? 0;

        if ($validated['quantity'] > $availableStock) {
            return response()->json([
                'message' => "Només hi ha {$availableStock} packs disponibles.",
            ], 422);
        }

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'in_cart',
                'installation_address' => $user->address ?? 'Pendent de confirmar',
                'shipping_address' => $user->address ?? 'Pendent de confirmar',
                'payment_method' => 'bizum',
            ]);
        }

        $currentPack = $order->packs()
            ->where('packs.id', $validated['pack_id'])
            ->first();

        if ($currentPack) {
            $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

            return response()->json([
                'added' => false,
                'message' => 'Aquest pack ja és al carret.',
                'order' => $order,
            ]);
        }

        $order->packs()->attach($validated['pack_id'], [
            'quantity' => $validated['quantity'],
        ]);

        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json([
            'added' => true,
            'order' => $order,
        ]);
    }

    /**
     * Create an order from checkout data. Supports guests and authenticated users.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer.name' => 'required|string|max:255',
            'customer.last_name_one' => 'required|string|max:255',
            'customer.last_name_second' => 'nullable|string|max:255',
            'customer.dni' => 'nullable|string|max:20',
            'customer.phone' => 'nullable|string|max:20',
            'customer.email' => 'required|string|email|max:255',
            'customer.address' => 'required|string|max:255',
            'customer.zip_code' => 'required|string|max:10',
            'order.installation_address' => 'required|string|max:255',
            'order.shipping_address' => 'required|string|max:255',
            'order.payment_method' => 'required|in:paypal,card,bizum',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,pack',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $customer = $validated['customer'];
        $orderData = $validated['order'];
        $productItems = [];
        $packItems = [];

        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'pack') {
                $pack = Pack::with('products')->findOrFail($item['id']);
                $availableStock = $pack->products->min('stock') ?? 0;

                if ($item['quantity'] > $availableStock) {
                    return response()->json([
                        'message' => "Només hi ha {$availableStock} packs disponibles per {$pack->name}.",
                    ], 422);
                }

                $packItems[$pack->id] = ['quantity' => $item['quantity']];
            } else {
                $product = Product::findOrFail($item['id']);

                if ($item['quantity'] > $product->stock) {
                    return response()->json([
                        'message' => "Només hi ha {$product->stock} unitats disponibles per {$product->name}.",
                    ], 422);
                }

                $productItems[$product->id] = ['quantity' => $item['quantity']];
            }
        }

        $order = Order::create([
            'user_id' => $user?->id,
            'status' => 'pending',
            'customer_name' => $customer['name'],
            'customer_last_name_one' => $customer['last_name_one'],
            'customer_last_name_second' => $customer['last_name_second'] ?? null,
            'customer_dni' => $customer['dni'] ?? null,
            'customer_phone' => $customer['phone'] ?? null,
            'customer_email' => $customer['email'],
            'customer_address' => $customer['address'],
            'customer_zip_code' => $customer['zip_code'],
            'installation_address' => $orderData['installation_address'],
            'shipping_address' => $orderData['shipping_address'],
            'payment_method' => $orderData['payment_method'],
        ]);

        $order->products()->attach($productItems);
        $order->packs()->attach($packItems);

        $order->load(['user', 'products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json($order, 201);
    }

    /**
     * Update a product quantity in the authenticated user's current cart.
     */
    public function updateCartProduct(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $product = Product::findOrFail($productId);

        if ($validated['quantity'] > $product->stock) {
            return response()->json([
                'message' => "Només hi ha {$product->stock} unitats disponibles.",
            ], 422);
        }

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->firstOrFail();

        $order->products()->updateExistingPivot($productId, [
            'quantity' => $validated['quantity'],
        ]);

        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json($order);
    }

    /**
     * Update a pack quantity in the authenticated user's current cart.
     */
    public function updateCartPack(Request $request, int $packId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $pack = Pack::with('products')->findOrFail($packId);
        $availableStock = $pack->products->min('stock') ?? 0;

        if ($validated['quantity'] > $availableStock) {
            return response()->json([
                'message' => "Només hi ha {$availableStock} packs disponibles.",
            ], 422);
        }

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->firstOrFail();

        $order->packs()->updateExistingPivot($packId, [
            'quantity' => $validated['quantity'],
        ]);

        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json($order);
    }

    /**
     * Remove a product from the authenticated user's current cart.
     */
    public function removeCartProduct(int $productId): JsonResponse
    {
        $user = auth()->user();

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->firstOrFail();

        $order->products()->detach($productId);
        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json($order);
    }

    /**
     * Remove a pack from the authenticated user's current cart.
     */
    public function removeCartPack(int $packId): JsonResponse
    {
        $user = auth()->user();

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->firstOrFail();

        $order->packs()->detach($packId);
        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json($order);
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
        $order = Order::with(['user', 'products', 'packs'])->findOrFail($id);
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
            'customer_name' => 'sometimes|string|max:255',
            'customer_last_name_one' => 'sometimes|string|max:255',
            'customer_last_name_second' => 'nullable|string|max:255',
            'customer_dni' => 'nullable|string|max:20',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'sometimes|string|email|max:255',
            'customer_address' => 'sometimes|string|max:255',
            'customer_zip_code' => 'sometimes|string|max:10',
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

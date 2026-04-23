<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommerceSetting;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

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
        $user = Auth::user();

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
        $user = Auth::user();

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
            'installation_requested' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($validated['product_id']);

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->first();

        if (! $order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'in_cart',
                'installation_address' => $user->shipping_address ?? 'Pendent de confirmar',
                'shipping_address' => $user->shipping_address ?? 'Pendent de confirmar',
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

        $this->validateCartStock($order, 'product', $product->id, $validated['quantity']);

        $order->products()->attach($validated['product_id'], [
            'quantity' => $validated['quantity'],
            'installation_requested' => $product->is_installable && (bool) ($validated['installation_requested'] ?? false),
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

        $user = Auth::user();
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

        if (! $order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'in_cart',
                'installation_address' => $user->shipping_address ?? 'Pendent de confirmar',
                'shipping_address' => $user->shipping_address ?? 'Pendent de confirmar',
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

        $this->validateCartStock($order, 'pack', $pack->id, $validated['quantity']);

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
     * Merge the guest cart into the authenticated user's current cart.
     */
    public function mergeCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:product,pack',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.installation_requested' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $skippedItems = [];

        $order = Order::where('user_id', $user->id)
            ->where('status', 'in_cart')
            ->latest()
            ->first();

        if (! $order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'in_cart',
                'installation_address' => $user->shipping_address ?? 'Pendent de confirmar',
                'shipping_address' => $user->shipping_address ?? 'Pendent de confirmar',
                'payment_method' => 'bizum',
            ]);
        }

        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'pack') {
                $this->mergePackCartItem($order, $item, $skippedItems);
            } else {
                $this->mergeProductCartItem($order, $item, $skippedItems);
            }
        }

        $order->load(['products.category', 'products.images', 'packs.images', 'packs.products']);

        return response()->json([
            'order' => $order,
            'skipped_items' => $skippedItems,
        ]);
    }

    private function mergeProductCartItem(Order $order, array $item, array &$skippedItems): void
    {
        $product = Product::find($item['id']);

        if (! $product || $product->stock <= 0) {
            $skippedItems[] = $item;

            return;
        }

        $quantity = min($item['quantity'], $product->stock);
        $currentProduct = $order->products()
            ->where('products.id', $product->id)
            ->first();

        if ($currentProduct) {
            if ((int) $currentProduct->pivot->quantity < $quantity) {
                $order->products()->updateExistingPivot($product->id, [
                    'quantity' => $quantity,
                    'installation_requested' => $product->is_installable && (bool) ($item['installation_requested'] ?? false),
                ]);
            } elseif (array_key_exists('installation_requested', $item)) {
                $order->products()->updateExistingPivot($product->id, [
                    'installation_requested' => $product->is_installable && (bool) $item['installation_requested'],
                ]);
            }

            return;
        }

        $order->products()->attach($product->id, [
            'quantity' => $quantity,
            'installation_requested' => $product->is_installable && (bool) ($item['installation_requested'] ?? false),
        ]);
    }

    private function mergePackCartItem(Order $order, array $item, array &$skippedItems): void
    {
        $pack = Pack::with('products')->find($item['id']);
        $availableStock = $pack?->products->min('stock') ?? 0;

        if (! $pack || $availableStock <= 0) {
            $skippedItems[] = $item;

            return;
        }

        $quantity = min($item['quantity'], $availableStock);
        $currentPack = $order->packs()
            ->where('packs.id', $pack->id)
            ->first();

        if ($currentPack) {
            if ((int) $currentPack->pivot->quantity < $quantity) {
                $order->packs()->updateExistingPivot($pack->id, [
                    'quantity' => $quantity,
                ]);
            }

            return;
        }

        $order->packs()->attach($pack->id, [
            'quantity' => $quantity,
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
            'customer.province' => 'nullable|string|max:255',
            'customer.country' => 'nullable|string|max:255',
            'order.installation_address' => 'required|string|max:255',
            'order.installation_zip_code' => 'nullable|string|max:10',
            'order.installation_province' => 'nullable|string|max:255',
            'order.installation_country' => 'nullable|string|max:255',
            'order.shipping_address' => 'required|string|max:255',
            'order.shipping_zip_code' => 'nullable|string|max:10',
            'order.shipping_province' => 'nullable|string|max:255',
            'order.shipping_country' => 'nullable|string|max:255',
            'order.payment_method' => 'required|in:paypal,card,bizum,bank_transfer',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,pack',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.installation_requested' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $customer = $validated['customer'];
            $orderData = $validated['order'];
            $productItems = [];
            $packItems = [];

            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'pack') {
                    $packItems[$item['id']] = ['quantity' => $item['quantity']];
                } else {
                    $product = Product::findOrFail($item['id']);
                    $productItems[$item['id']] = [
                        'quantity' => $item['quantity'],
                        'installation_requested' => $product->is_installable && (bool) ($item['installation_requested'] ?? false),
                    ];
                }
            }

            $this->reserveProductStockItems($productItems);
            $this->reservePackStockItems($packItems);

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
                'customer_province' => $customer['province'] ?? null,
                'customer_country' => $customer['country'] ?? 'España',
                'installation_address' => $orderData['installation_address'],
                'installation_zip_code' => $orderData['installation_zip_code'] ?? null,
                'installation_province' => $orderData['installation_province'] ?? null,
                'installation_country' => $orderData['installation_country'] ?? 'España',
                'shipping_address' => $orderData['shipping_address'],
                'shipping_zip_code' => $orderData['shipping_zip_code'] ?? null,
                'shipping_province' => $orderData['shipping_province'] ?? null,
                'shipping_country' => $orderData['shipping_country'] ?? 'España',
                'shipping_price' => CommerceSetting::current()->shipping_price,
                'installation_price' => $this->getInstallationPrice($productItems, $packItems),
                'payment_method' => $orderData['payment_method'],
            ]);

            $order->products()->attach($productItems);
            $order->packs()->attach($packItems);
            $order->load(['user', 'products.category', 'products.images', 'packs.images', 'packs.products']);

            DB::commit();
        } catch (\Throwable $error) {
            DB::rollBack();
            throw $error;
        }

        return response()->json($order, 201);
    }

    private function reserveOrderStock(Order $order): void
    {
        $order->loadMissing(['products', 'packs.products']);

        $productItems = [];
        $packItems = [];

        foreach ($order->products as $product) {
            $productItems[$product->id] = [
                'quantity' => (int) $product->pivot->quantity,
                'installation_requested' => (bool) $product->pivot->installation_requested,
            ];
        }

        foreach ($order->packs as $pack) {
            $packItems[$pack->id] = [
                'quantity' => (int) $pack->pivot->quantity,
            ];
        }

        $this->reserveProductStockItems($productItems);
        $this->reservePackStockItems($packItems);
    }

    private function getInstallationPrice(array $productItems, array $packItems): float
    {
        $hasInstallation = collect($productItems)->contains(fn ($item) => (bool) ($item['installation_requested'] ?? false));

        if (! $hasInstallation) {
            return 0;
        }

        $subtotal = 0;

        foreach ($productItems as $productId => $item) {
            $product = Product::findOrFail($productId);
            $subtotal += $this->getProductSalePrice($product) * (int) $item['quantity'];
        }

        foreach ($packItems as $packId => $item) {
            $pack = Pack::findOrFail($packId);
            $subtotal += (float) $pack->total_price * (int) $item['quantity'];
        }

        foreach (CommerceSetting::current()->installation_rules ?? [] as $rule) {
            $min = (float) ($rule['min_subtotal'] ?? 0);
            $max = $rule['max_subtotal'] ?? null;

            if ($subtotal >= $min && ($max === null || $subtotal <= (float) $max)) {
                return (float) ($rule['price'] ?? 0);
            }
        }

        return 0;
    }

    private function getProductSalePrice(Product $product): float
    {
        $price = (float) $product->price;
        $discount = (float) ($product->discount ?? 0);

        return $discount <= 0 ? $price : $price * (1 - $discount / 100);
    }

    private function validateCartStock(Order $order, string $itemType, int $itemId, int $quantity): void
    {
        $order->loadMissing(['products', 'packs.products']);

        $productItems = [];
        $packItems = [];
        $candidateFound = false;

        foreach ($order->products as $product) {
            $itemQuantity = (int) $product->pivot->quantity;

            if ($itemType === 'product' && $product->id === $itemId) {
                $itemQuantity = $quantity;
                $candidateFound = true;
            }

            $productItems[$product->id] = ['quantity' => $itemQuantity];
        }

        foreach ($order->packs as $pack) {
            $itemQuantity = (int) $pack->pivot->quantity;

            if ($itemType === 'pack' && $pack->id === $itemId) {
                $itemQuantity = $quantity;
                $candidateFound = true;
            }

            $packItems[$pack->id] = ['quantity' => $itemQuantity];
        }

        if (! $candidateFound) {
            if ($itemType === 'pack') {
                $packItems[$itemId] = ['quantity' => $quantity];
            } else {
                $productItems[$itemId] = ['quantity' => $quantity];
            }
        }

        $this->validateStockItems($productItems, $packItems);
    }

    private function validateStockItems(array $productItems, array $packItems): void
    {
        $requiredStockByProduct = [];
        $packNamesByProduct = [];

        foreach ($productItems as $productId => $item) {
            $requiredStockByProduct[$productId] = ($requiredStockByProduct[$productId] ?? 0) + (int) $item['quantity'];
        }

        foreach ($packItems as $packId => $item) {
            $quantity = (int) $item['quantity'];
            $pack = Pack::with('products')->findOrFail($packId);

            foreach ($pack->products as $packProduct) {
                $requiredStockByProduct[$packProduct->id] = ($requiredStockByProduct[$packProduct->id] ?? 0) + $quantity;
                $packNamesByProduct[$packProduct->id][] = $pack->name;
            }
        }

        foreach ($requiredStockByProduct as $productId => $requiredQuantity) {
            $product = Product::findOrFail($productId);

            if ($requiredQuantity > $product->stock) {
                $packNames = array_unique($packNamesByProduct[$productId] ?? []);
                $packText = count($packNames) > 0 ? ' en els packs '.implode(', ', $packNames) : '';

                throw ValidationException::withMessages([
                    'stock' => "No hi ha prou estoc de {$product->name}{$packText}. Hi ha {$product->stock} unitats disponibles i el carret en necessita {$requiredQuantity}.",
                ]);
            }
        }
    }

    private function reserveProductStockItems(array $productItems): void
    {
        foreach ($productItems as $productId => $item) {
            $quantity = (int) $item['quantity'];
            $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'stock' => "Només hi ha {$product->stock} unitats disponibles per {$product->name}.",
                ]);
            }

            $product->decrement('stock', $quantity);
        }
    }

    private function reservePackStockItems(array $packItems): void
    {
        foreach ($packItems as $packId => $item) {
            $quantity = (int) $item['quantity'];
            $pack = Pack::with('products')->findOrFail($packId);

            foreach ($pack->products as $packProduct) {
                $product = Product::whereKey($packProduct->id)->lockForUpdate()->firstOrFail();

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'stock' => "Només hi ha {$product->stock} packs disponibles per {$pack->name}.",
                    ]);
                }

                $product->decrement('stock', $quantity);
            }
        }
    }

    /**
     * Update a product quantity in the authenticated user's current cart.
     */
    public function updateCartProduct(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'installation_requested' => 'nullable|boolean',
        ]);

        $user = Auth::user();
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

        $this->validateCartStock($order, 'product', $productId, $validated['quantity']);

        $pivotData = ['quantity' => $validated['quantity']];

        if (array_key_exists('installation_requested', $validated)) {
            $pivotData['installation_requested'] = $product->is_installable && (bool) $validated['installation_requested'];
        }

        $order->products()->updateExistingPivot($productId, $pivotData);

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

        $user = Auth::user();
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

        $this->validateCartStock($order, 'pack', $packId, $validated['quantity']);

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
        $user = Auth::user();

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
        $user = Auth::user();

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
            'payment_method' => 'required|in:paypal,card,bizum,bank_transfer',
            'status' => 'nullable|in:in_cart,pending,shipped,installation_confirmed,installation_pending',
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
            'payment_method' => 'sometimes|in:paypal,card,bizum,bank_transfer',
            'status' => 'sometimes|in:in_cart,pending,shipped,installation_confirmed,installation_pending',
            'shipped_at' => 'nullable|date',
            'installation_scheduled_at' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $nextStatus = $validated['status'] ?? $order->status;

            // Auto-set shipped_at when transitioning to shipped (if not already set)
            if ($nextStatus === 'shipped' && is_null($order->shipped_at)) {
                $validated['shipped_at'] = now();
            }

            // Auto-clear shipped_at when changing to pending
            if ($nextStatus === 'pending') {
                $validated['shipped_at'] = null;
            }

            // Auto-clear installation_scheduled_at when status changes to installation_pending
            if ($nextStatus === 'installation_pending') {
                $validated['installation_scheduled_at'] = null;
            }

            // Auto-set status to installation_confirmed if installation_scheduled_at is provided
            if (isset($validated['installation_scheduled_at']) && $validated['installation_scheduled_at'] !== null) {
                $validated['status'] = 'installation_confirmed';
                $nextStatus = 'installation_confirmed';
            }

            if ($order->status === 'in_cart' && $nextStatus === 'pending') {
                $this->reserveOrderStock($order);
                $setting = CommerceSetting::current();
                $order->loadMissing(['products', 'packs.products']);

                $productItems = [];
                $packItems = [];

                foreach ($order->products as $product) {
                    $productItems[$product->id] = [
                        'quantity' => (int) $product->pivot->quantity,
                        'installation_requested' => (bool) $product->pivot->installation_requested,
                    ];
                }

                foreach ($order->packs as $pack) {
                    $packItems[$pack->id] = [
                        'quantity' => (int) $pack->pivot->quantity,
                    ];
                }

                $validated['shipping_price'] = $setting->shipping_price;
                $validated['installation_price'] = $this->getInstallationPrice($productItems, $packItems);
            }

            $order->update($validated);

            DB::commit();
        } catch (\Throwable $error) {
            DB::rollBack();
            throw $error;
        }

        $order->load(['user', 'products.category', 'products.images', 'packs.images', 'packs.products']);

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

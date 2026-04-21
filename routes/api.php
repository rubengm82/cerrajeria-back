<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PackController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CustomSolutionController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\PackImageController;
use App\Http\Controllers\Api\FeatureTypeController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\AlbaranController;


/* *************** */
/* Rutas públicas  */
/* *************** */

// Ruta Login
Route::post('/login', [AuthController::class, 'login']);

// Ruta Registro (con verificación de email)
Route::post('/register', [AuthController::class, 'register']);

// Reenviar email de verificación (sin estar logueado)
Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);

// Rutas de recuperación de contraseña
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Rutas de verificación de email
Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

// Rutas publicas de soluciones personalizadas
Route::get('/custom-solutions', [CustomSolutionController::class, 'index']);
Route::post('/custom-solutions', [CustomSolutionController::class, 'store']);



/* ***************** */
/* Rutas protegidas  */
/* ***************** */

// Rutas de categorías - PROTEGIDAS (solo admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories/with-trashed', [CategoryController::class, 'indexWithTrashed']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('/categories/trashed', [CategoryController::class, 'trashed']);
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']);
});

// Rutas de categorías - PÚBLICAS
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/important', [CategoryController::class, 'getImportantCategories']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Rutas de productos - PÚBLICAS
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/important', [ProductController::class, 'getImportantProducts']);
Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');

// Rutas de búsqueda - PÚBLICAS
Route::get('/search', [App\Http\Controllers\Api\SearchController::class, 'search']);
Route::get('/search/quick', [App\Http\Controllers\Api\SearchController::class, 'quickSearch']);

// Rutas de productos - PROTEGIDAS (solo admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products/with-trashed', [ProductController::class, 'indexWithTrashed']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/products/trashed', [ProductController::class, 'trashed']);
    Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
    Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete']);
});

// Rutas de packs - PÚBLICAS
Route::get('/packs', [PackController::class, 'index']);

// Rutas de packs - PROTEGIDAS (solo admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/packs/with-trashed', [PackController::class, 'indexWithTrashed']);
    Route::post('/packs', [PackController::class, 'store']);
    Route::put('/packs/{id}', [PackController::class, 'update']);
    Route::delete('/packs/{id}', [PackController::class, 'destroy']);
    Route::get('/packs/trashed', [PackController::class, 'trashed']);
    Route::post('/packs/{id}/restore', [PackController::class, 'restore']);
    Route::delete('/packs/{id}/force', [PackController::class, 'forceDelete']);
});
Route::get('/packs/{id}', [PackController::class, 'show']);

// Checkout público: permite comprar con o sin usuario autenticado
Route::post('/checkout/orders', [OrderController::class, 'checkout']);

// Rutas de pedidos
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/with-trashed', [OrderController::class, 'indexWithTrashed']);
    Route::get('/orders/cart', [OrderController::class, 'cart']);
    Route::post('/orders/cart/merge', [OrderController::class, 'mergeCart']);
    Route::post('/orders/cart/products', [OrderController::class, 'addProductToCart']);
    Route::put('/orders/cart/products/{productId}', [OrderController::class, 'updateCartProduct']);
    Route::delete('/orders/cart/products/{productId}', [OrderController::class, 'removeCartProduct']);
    Route::post('/orders/cart/packs', [OrderController::class, 'addPackToCart']);
    Route::put('/orders/cart/packs/{packId}', [OrderController::class, 'updateCartPack']);
    Route::delete('/orders/cart/packs/{packId}', [OrderController::class, 'removeCartPack']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::get('/orders/trashed', [OrderController::class, 'trashed']);
    Route::post('/orders/{id}/restore', [OrderController::class, 'restore']);
    Route::delete('/orders/{id}/force', [OrderController::class, 'forceDelete']);
});


// Rutas de imágenes de productos
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/product-images', [ProductImageController::class, 'index']);
    Route::post('/product-images', [ProductImageController::class, 'store']);
    Route::get('/product-images/{id}', [ProductImageController::class, 'show']);
    Route::put('/product-images/{id}', [ProductImageController::class, 'update']);
    Route::delete('/product-images/{id}', [ProductImageController::class, 'destroy']);
});

// Rutas de imágenes de packs
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pack-images', [PackImageController::class, 'index']);
    Route::post('/pack-images', [PackImageController::class, 'store']);
    Route::get('/pack-images/{id}', [PackImageController::class, 'show']);
    Route::put('/pack-images/{id}', [PackImageController::class, 'update']);
    Route::delete('/pack-images/{id}', [PackImageController::class, 'destroy']);
});

// Rutas de tipos de características - PÚBLICAS
Route::get('/feature-types', [FeatureTypeController::class, 'index']);

// Rutas de características - PÚBLICAS
Route::get('/features', [FeatureController::class, 'index']);

// Rutas protegidas de tipos de características
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/feature-types/with-trashed', [FeatureTypeController::class, 'indexWithTrashed']);
    Route::post('/feature-types', [FeatureTypeController::class, 'store']);
    Route::get('/feature-types/{id}', [FeatureTypeController::class, 'show']);
    Route::put('/feature-types/{id}', [FeatureTypeController::class, 'update']);
    Route::delete('/feature-types/{id}', [FeatureTypeController::class, 'destroy']);
    Route::get('/feature-types/trashed', [FeatureTypeController::class, 'trashed']);
    Route::post('/feature-types/{id}/restore', [FeatureTypeController::class, 'restore']);
    Route::delete('/feature-types/{id}/force', [FeatureTypeController::class, 'forceDelete']);
});

// Rutas protegidas de características
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/features/with-trashed', [FeatureController::class, 'indexWithTrashed']);
    Route::post('/features', [FeatureController::class, 'store']);
    Route::get('/features/{id}', [FeatureController::class, 'show']);
    Route::put('/features/{id}', [FeatureController::class, 'update']);
    Route::delete('/features/{id}', [FeatureController::class, 'destroy']);
    Route::get('/features/trashed', [FeatureController::class, 'trashed']);
    Route::post('/features/{id}/restore', [FeatureController::class, 'restore']);
    Route::delete('/features/{id}/force', [FeatureController::class, 'forceDelete']);
});

// Rutas de usuarios
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Rutas de soluciones personalizadas (protegidas - solo admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/custom-solutions/with-trashed', [CustomSolutionController::class, 'indexWithTrashed']);
    Route::get('/custom-solutions/trashed', [CustomSolutionController::class, 'trashed']);
    Route::get('/custom-solutions/{id}', [CustomSolutionController::class, 'show']);
    Route::put('/custom-solutions/{id}', [CustomSolutionController::class, 'update']);
    Route::delete('/custom-solutions/{id}', [CustomSolutionController::class, 'destroy']);
    Route::post('/custom-solutions/{id}/restore', [CustomSolutionController::class, 'restore']);
    Route::delete('/custom-solutions/{id}/force', [CustomSolutionController::class, 'forceDelete']);
});

// Ruta para descargar albaranes (protegida)
Route::middleware('auth:sanctum')->get('/albaranes/{id}/download', [AlbaranController::class, 'download']);

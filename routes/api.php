<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PackController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomSolutionController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\PackImageController;
use App\Http\Controllers\Api\ProductFeatureTypeController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\CustomSolutionFileController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas de categorías (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Rutas de productos (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

// Rutas de packs (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/packs', [PackController::class, 'index']);
    Route::post('/packs', [PackController::class, 'store']);
    Route::get('/packs/{id}', [PackController::class, 'show']);
    Route::put('/packs/{id}', [PackController::class, 'update']);
    Route::delete('/packs/{id}', [PackController::class, 'destroy']);
});

// Rutas de pedidos (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});

// Rutas de soluciones personalizadas (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/custom-solutions', [CustomSolutionController::class, 'index']);
    Route::post('/custom-solutions', [CustomSolutionController::class, 'store']);
    Route::get('/custom-solutions/{id}', [CustomSolutionController::class, 'show']);
    Route::put('/custom-solutions/{id}', [CustomSolutionController::class, 'update']);
    Route::delete('/custom-solutions/{id}', [CustomSolutionController::class, 'destroy']);
});

// Rutas de imágenes de productos (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/product-images', [ProductImageController::class, 'index']);
    Route::post('/product-images', [ProductImageController::class, 'store']);
    Route::get('/product-images/{id}', [ProductImageController::class, 'show']);
    Route::put('/product-images/{id}', [ProductImageController::class, 'update']);
    Route::delete('/product-images/{id}', [ProductImageController::class, 'destroy']);
});

// Rutas de imágenes de packs (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pack-images', [PackImageController::class, 'index']);
    Route::post('/pack-images', [PackImageController::class, 'store']);
    Route::get('/pack-images/{id}', [PackImageController::class, 'show']);
    Route::put('/pack-images/{id}', [PackImageController::class, 'update']);
    Route::delete('/pack-images/{id}', [PackImageController::class, 'destroy']);
});

// Rutas de tipos de características (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/product-feature-types', [ProductFeatureTypeController::class, 'index']);
    Route::post('/product-feature-types', [ProductFeatureTypeController::class, 'store']);
    Route::get('/product-feature-types/{id}', [ProductFeatureTypeController::class, 'show']);
    Route::put('/product-feature-types/{id}', [ProductFeatureTypeController::class, 'update']);
    Route::delete('/product-feature-types/{id}', [ProductFeatureTypeController::class, 'destroy']);
});

// Rutas de características (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/features', [FeatureController::class, 'index']);
    Route::post('/features', [FeatureController::class, 'store']);
    Route::get('/features/{id}', [FeatureController::class, 'show']);
    Route::put('/features/{id}', [FeatureController::class, 'update']);
    Route::delete('/features/{id}', [FeatureController::class, 'destroy']);
});

// Rutas de archivos de soluciones personalizadas (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/custom-solution-files', [CustomSolutionFileController::class, 'index']);
    Route::post('/custom-solution-files', [CustomSolutionFileController::class, 'store']);
    Route::get('/custom-solution-files/{id}', [CustomSolutionFileController::class, 'show']);
    Route::put('/custom-solution-files/{id}', [CustomSolutionFileController::class, 'update']);
    Route::delete('/custom-solution-files/{id}', [CustomSolutionFileController::class, 'destroy']);
});

// Rutas de usuarios (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::get('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

// Protected routes (require JWT token)
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'add']);
    Route::put('cart/items/{product}', [CartController::class, 'update']);
    Route::delete('cart/items/{product}', [CartController::class, 'remove']);
    Route::delete('cart', [CartController::class, 'clear']);
});

Route::prefix('admin')
    ->middleware(['auth:api', 'can:admin'])
    ->group(function () {
        Route::apiResource('products', AdminProductController::class)
            ->parameters(['products' => 'product']);
    });

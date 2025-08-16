<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::get('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'add']);
    Route::put('cart/items/{product}', [CartController::class, 'update']);
    Route::delete('cart/items/{product}', [CartController::class, 'remove']);
    Route::delete('cart', [CartController::class, 'clear']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);

    Route::middleware('can:admin')
        ->prefix('admin')
        ->group(function () {
            Route::get('products', [AdminProduct::class, 'index'])->name('products.index');
            Route::post('products', [AdminProduct::class, 'store'])->name('products.store');
            Route::get('products/{product}', [AdminProduct::class, 'show'])->name('products.show');
            Route::put('products/{product}', [AdminProduct::class, 'update'])->name('products.update');
            Route::delete('products/{product}', [AdminProduct::class, 'destroy'])->name('products.destroy');
            Route::patch('products/{product}/toggle', [AdminProduct::class, 'toggle'])->name('products.toggle');

            Route::get('orders', [AdminOrder::class, 'index'])->name('orders.index');
            Route::patch('orders/{order}/status', [AdminOrder::class, 'updateStatus'])->name('orders.updateStatus');
        });

});


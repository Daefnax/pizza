<?php

namespace App\Providers;


use App\Repositories\EloquentProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Services\CartService;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('api')->group(base_path('routes/api.php'));
        Gate::define('admin', fn ($user) => (bool) $user?->is_admin);
    }
}

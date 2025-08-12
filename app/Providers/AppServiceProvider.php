<?php

namespace App\Providers;

use App\Domain\Products\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentProductRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('api')->group(base_path('routes/api.php'));
    }
}

<?php

namespace App\Providers;


use App\Models\User;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('api')->group(base_path('routes/api.php'));
        Gate::define('admin', static fn (User $user) => (bool) $user->is_admin);
    }
}

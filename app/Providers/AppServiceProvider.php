<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Custom Blade directive for permission checking
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Custom Blade directive for role checking
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}

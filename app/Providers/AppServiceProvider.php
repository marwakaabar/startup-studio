<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
    public function boot()
    {
        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'role' => Auth::user()->role,
                    ] : null,
                ];
            },
        ]);
    }
}

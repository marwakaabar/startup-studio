<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $user = Auth::user();
            $view->with('auth', [
                'user' => Auth::check() ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'approved' => $user->approved,
                    'profile_image' => $user->profile_image,
                    'isCoach' => $user->isCoach(),
                    'isStartup' => $user->isStartup(),
                    'isInvestisseur' => $user->isInvestisseur(),
                    'isAdmin' => $user->isAdmin(),
                    'coach' => $user->coach,
                    'startup' => $user->startup,
                    'investisseur' => $user->investisseur,
                    'admin' => $user->admin,
                ] : null,
            ]);
        });
    }
}

<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
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
        Inertia::share([
            'auth' => function () {
                $user = auth()->user();
                return [
                    'id' => session('id'),
                    'type' => session('type'),
                    'access' => session('access'),
                    'mei_id' => session('mei_id'),
                    'user' => $user,
                ];
            }
        ]);
    }
}

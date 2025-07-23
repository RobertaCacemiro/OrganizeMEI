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
    // public function boot(): void
    // {
    //     Inertia::share([
    //         'auth.user.profile_photo' => function () {
    //             $user = Auth::user();
    //             return $user?->meiProfile?->profile_photo;
    //         },
    //     ]);
    // }
}

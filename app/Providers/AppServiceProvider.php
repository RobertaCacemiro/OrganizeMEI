<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Mail\SendGridTransport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->app->make(MailManager::class)->extend('sendgrid', function ($config) {
            return new SendGridTransport(env('SENDGRID_API_KEY'), Log::channel('stack'));
        });

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Inertia::share([
            'app_url' => config('app.url'),
        ]);
    }
}

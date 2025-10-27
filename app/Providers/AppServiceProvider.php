<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Mail\SendGridTransport;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        $this->app->make(MailManager::class)->extend('sendgrid', function ($config) {
            return new SendGridTransport(env('SENDGRID_API_KEY'), Log::channel('stack'));
        });
    }
}

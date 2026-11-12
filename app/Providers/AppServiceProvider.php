<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        // Paksa HTTPS di production (Wasmer) dan saat lewat Ngrok
        if (
            config('app.env') === 'production'
            || str_contains(request()->url(), 'ngrok-free.dev')
        ) {
            URL::forceScheme('https');
        }
    }
}
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- WAJIB TAMBAHIN INI

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        // Paksa semua link pake HTTPS kalau diakses lewat Ngrok
        if (str_contains(request()->url(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
}
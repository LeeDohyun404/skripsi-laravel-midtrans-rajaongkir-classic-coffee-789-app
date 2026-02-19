<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarin alias satpam admin_auth di sini!
        $middleware->alias([
            'admin_auth' => \App\Http\Middleware\AdminAuth::class,
        ]);

        // ✅ TRUST ALL PROXIES (NGROK)
        $middleware->trustProxies(at: '*');

        // ✅ EXCLUDE EMAIL VERIFICATION & MIDTRANS DARI CSRF
        $middleware->validateCsrfTokens(except: [
            'api/midtrans-callback',
            'email/*',  // ✅ TAMBAH INI
        ]);

        $middleware->statefulApi(); 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
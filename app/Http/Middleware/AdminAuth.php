<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Cek tiket session
        if (!Session::has('admin_logged_in')) {
            return redirect('/admin/login')->with('error', 'Login dulu, Bree!');
        }

        return $next($request);
    }
}
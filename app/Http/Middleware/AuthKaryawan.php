<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthKaryawan
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('karyawan')->check()) {
            return redirect()->route('staff.login')
                ->with('error', 'Silakan login dulu!');
        }

        return $next($request);
    }
}
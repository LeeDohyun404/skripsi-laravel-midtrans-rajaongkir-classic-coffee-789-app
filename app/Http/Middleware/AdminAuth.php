<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah ada orang yang login di pintu 'staff'
        if (Auth::guard('staff')->check()) {
            
            // 2. Ambil data orangnya
            $user = Auth::guard('staff')->user();

            // 3. Cek apakah jabatannya 'admin'
            if ($user->peran === 'admin') {
                return $next($request); // Silakan masuk bos!
            }
        }

        // Kalau bukan admin atau belum login, tendang ke login-staff
        return redirect('/login-staff')->with('error', 'Khusus Admin, Man!');
    }
}
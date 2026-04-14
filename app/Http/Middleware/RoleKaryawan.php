<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleKaryawan
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::guard('karyawan')->user();

        if (!$user || $user->peran !== $role) {
            return redirect()->route('staff.login')
                ->with('error', 'Akses ditolak!');
        }

        return $next($request);
    }
}
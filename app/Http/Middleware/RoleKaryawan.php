<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleKaryawan
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('karyawan')->user();

        if (!$user) {
            return redirect()->route('staff.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Admin memiliki akses penuh ke seluruh fitur karyawan (operator, kasir, dll)
        if ($user->peran === 'admin') {
            return $next($request);
        }

        // Cek apakah peran user ada di daftar roles yang diizinkan
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }

        if (!in_array($user->peran, $allowedRoles)) {
            return redirect()->route('staff.login')
                ->with('error', 'Akses ditolak!');
        }

        return $next($request);
    }
}

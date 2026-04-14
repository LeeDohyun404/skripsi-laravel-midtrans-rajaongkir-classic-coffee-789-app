<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanAuthController extends Controller
{
    public function showLogin()
    {
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('karyawan')->attempt($credentials)) {
            $request->session()->regenerate();

             $peran = Auth::guard('karyawan')->user()->peran; // ✅ ganti role → peran

            return match($peran) {
                'admin'    => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                'kasir'    => redirect()->route('kasir.dashboard'),
                default    => redirect()->route('staff.login')->with('error', 'Role tidak dikenali!'),
            };
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function logout(Request $request)
    {
        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('success', 'Berhasil logout!');
    }
}
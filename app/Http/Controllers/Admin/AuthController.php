<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
        // Cek input vs data yang lo mau
        if ($request->username == 'admin' && $request->password == 'kopi789') {
            // Kalau bener, kasih "tiket" session
            Session::put('admin_logged_in', true);
            return redirect('/admin/history');
        }

        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout() {
        Session::forget('admin_logged_in');
        return redirect('/admin/login');
    }
}
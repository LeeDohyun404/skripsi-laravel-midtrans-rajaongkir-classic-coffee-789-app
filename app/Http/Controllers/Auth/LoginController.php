<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Blokir user yang belum verifikasi email (termasuk email fiktif)
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Login gagal! Email ini belum diverifikasi. Email fiktif atau email yang belum dikonfirmasi tidak dapat digunakan untuk login. Silakan cek inbox atau folder spam email Anda.');
        }
    }
}


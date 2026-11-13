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
    protected $redirectTo = '/produk';

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
     * Pastikan format email bersih dari spasi dan huruf kapital tidak sengaja
     */
    protected function credentials(Request $request)
    {
        return [
            'email' => strtolower(trim((string) $request->email)),
            'password' => (string) $request->password,
        ];
    }

    /**
     * Blokir user yang belum verifikasi email (termasuk email fiktif)
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {}

            Auth::logout();
            return redirect()->route('login')->with('error', 'Login gagal! Akun Anda belum aktif karena email belum diverifikasi. Kami telah mengirimkan ulang link verifikasi ke ' . $user->email . '. Silakan buka email Anda dan klik tombol verifikasi.');
        }
    }
}



<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['prompt' => 'select_account']) // selalu tampilkan pilihan akun Google
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrNew(['email' => $googleUser->getEmail()]);

            $user->name              = $user->name ?: $googleUser->getName();
            $user->google_id         = $googleUser->getId();
            $user->avatar            = $user->avatar ?: $googleUser->getAvatar();
            $user->email_verified_at = $user->email_verified_at ?? now();

            if (!$user->exists) {
                // Akun baru dari Google: password acak, karena kolom wajib diisi
                $user->password = Str::random(32);
            }

            $user->save();

            Auth::login($user); // tanpa remember-me permanen

            return redirect('/produk');

        } catch (\Throwable $e) {
            Log::error('Google login gagal: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login Google gagal, silakan coba lagi.');
        }
    }
}
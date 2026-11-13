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

            $googleEmail = $googleUser->getEmail();

            // ── Tolak email fiktif / non-Gmail ──
            // Hanya izinkan domain Gmail asli (gmail.com & googlemail.com)
            $allowedDomains = ['gmail.com', 'googlemail.com'];
            $emailDomain = strtolower(substr(strrchr($googleEmail, '@'), 1));
            if (! in_array($emailDomain, $allowedDomains)) {
                return redirect('/login')->with('error', 'Hanya akun Gmail asli yang diizinkan untuk login.');
            }

            // Pastikan Google mengembalikan email yang terverifikasi
            $googleRaw = $googleUser->getRaw();
            if (empty($googleRaw['email_verified'])) {
                return redirect('/login')->with('error', 'Email Google Anda belum diverifikasi. Silakan verifikasi email di Google terlebih dahulu.');
            }

            $user = User::firstOrNew(['email' => $googleEmail]);

            $user->name              = $user->name ?: $googleUser->getName();
            $user->google_id         = $googleUser->getId();
            $user->avatar            = $user->avatar ?: $googleUser->getAvatar();
            $user->email_verified_at = $user->email_verified_at ?? now();

            if (!$user->exists) {
                // Akun baru dari Google: password NULL, menandai akun Google-only
                $user->password = null;
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
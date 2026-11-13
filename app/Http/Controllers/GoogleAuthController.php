<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
            $user->email_verified_at = $user->email_verified_at ?? now();

            // Download & proxy avatar Google ke database kita agar tidak bergantung URL Google
            if (!$user->avatar || str_starts_with($user->avatar, 'https://lh')) {
                $googleAvatarUrl = $googleUser->getAvatar();
                $proxied = self::downloadAndSaveAvatar($googleAvatarUrl);
                $user->avatar = $proxied ?: $googleAvatarUrl;
            }

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

    /**
     * Download avatar dari URL Google dan simpan ke tabel product_images.
     * Kembalikan URL lokal /foto-avatar/{filename}, atau null jika gagal.
     */
    public static function downloadAndSaveAvatar(string $url): ?string
    {
        try {
            // Tambah parameter =s200 untuk ukuran yang lebih kecil
            $baseUrl = preg_replace('/=s\d+(-c)?$/', '', $url);
            $fetchUrl = $baseUrl . '=s200-c';

            // Coba dengan curl dulu (lebih andal di Wasmer)
            $data = null;
            if (function_exists('curl_init')) {
                $ch = curl_init($fetchUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Classic789Bot/1.0)',
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $data = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if (!$data || $httpCode !== 200) $data = null;
            }

            // Fallback ke file_get_contents
            if (!$data) {
                $context = stream_context_create(['http' => [
                    'timeout'         => 8,
                    'follow_location' => true,
                    'user_agent'      => 'Mozilla/5.0 (compatible; Classic789Bot/1.0)',
                ]]);
                $data = @file_get_contents($fetchUrl, false, $context);
            }

            if (!$data || strlen($data) < 100) return null;

            $mime = 'image/jpeg';
            if (str_starts_with($data, "\x89PNG")) $mime = 'image/png';
            elseif (str_starts_with($data, "GIF")) $mime = 'image/gif';
            $ext = match ($mime) { 'image/png' => 'png', 'image/gif' => 'gif', default => 'jpg' };

            if (!Schema::hasTable('product_images')) {
                Schema::create('product_images', function ($table) {
                    $table->id();
                    $table->string('filename')->unique();
                    $table->string('mime', 100);
                    $table->longText('data');
                    $table->timestamps();
                });
            }

            $filename = 'gv-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
            DB::table('product_images')->insert([
                'filename'   => $filename,
                'mime'       => $mime,
                'data'       => base64_encode($data),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return url('/foto-avatar') . '?f=' . $filename;
        } catch (\Throwable $e) {
            Log::warning('GoogleAuthController: gagal download avatar: ' . $e->getMessage());
            return null;
        }
    }
}
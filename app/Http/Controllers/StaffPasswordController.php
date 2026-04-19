<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;

class StaffPasswordController extends Controller
{
    // ----------------------------------------------------------------
    // STEP 1 — Tampilkan form "Lupa Password"
    // GET /staff/forgot-password
    // ----------------------------------------------------------------
    public function showForgotForm(): View
    {
        return view('staff.forgot-password'); // ✅ FIX: sesuai lokasi file
    }

    // ----------------------------------------------------------------
    // STEP 2 — Kirim link reset ke email karyawan
    // POST /staff/forgot-password
    // ----------------------------------------------------------------
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Gunakan broker 'karyawan' sesuai config/auth.php
        $status = Password::broker('karyawan')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }

        $errorMessages = [
            Password::INVALID_USER    => 'Email tidak terdaftar dalam sistem.',
            Password::RESET_THROTTLED => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.',
        ];

        $message = $errorMessages[$status] ?? 'Terjadi kesalahan. Silakan coba lagi.';

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }

    // ----------------------------------------------------------------
    // STEP 3 — Tampilkan form "Buat Password Baru"
    // GET /staff/reset-password/{token}?email=xxx
    // ----------------------------------------------------------------
    public function showResetForm(Request $request, string $token): View
    {
        return view('staff.reset-password', [ // ✅ FIX: hapus 'views.' di depan
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // ----------------------------------------------------------------
    // STEP 4 — Simpan password baru
    // POST /staff/reset-password
    // ----------------------------------------------------------------
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ], [
            'token.required'      => 'Token reset tidak valid.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'password.required'   => 'Password baru wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers'    => 'Password harus mengandung minimal satu angka.',
        ]);

        $status = Password::broker('karyawan')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($karyawan, string $password) {
                $karyawan->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($karyawan));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('staff.login')
                ->with('success', 'Password berhasil direset. Silakan masuk dengan password baru Anda.');
        }

        $errorMessages = [
            Password::INVALID_TOKEN => 'Tautan reset tidak valid atau sudah expired. Silakan minta tautan baru.',
            Password::INVALID_USER  => 'Email tidak ditemukan dalam sistem.',
        ];

        $message = $errorMessages[$status] ?? 'Terjadi kesalahan. Silakan coba lagi.';

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }
}
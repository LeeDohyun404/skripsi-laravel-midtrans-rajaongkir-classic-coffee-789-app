<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Where to redirect users after verification.
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
        // Hanya show dan resend yang butuh auth
        $this->middleware('auth')->only('show', 'resend');
        // verify TIDAK butuh auth karena link sudah ditandatangani secara kriptografis (signed URL)
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Tampilkan halaman notice verifikasi jika belum terverifikasi
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectTo)
            : view('auth.verify');
    }

    /**
     * Verifikasi email dari link yang diklik user di email mereka.
     * Dapat diverifikasi meskipun user sedang tidak login!
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Tautan verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect($this->redirectTo)->with('success', 'Email Anda sudah terverifikasi sebelumnya.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Otomatis login setelah email berhasil diverifikasi
        Auth::login($user);

        return redirect($this->redirectTo)->with('success', '🎉 Selamat! Email Anda berhasil diverifikasi. Akun Anda telah aktif dan siap digunakan.');
    }

    /**
     * Kirim ulang link verifikasi email
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}


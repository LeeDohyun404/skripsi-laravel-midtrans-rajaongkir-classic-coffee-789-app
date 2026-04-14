<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AktivasiKaryawanController extends Controller
{
    public function tampilForm(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('aktivasi.invalid')->with('error', 'Link aktivasi tidak valid atau sudah kedaluwarsa.');
        }

        $karyawan = Karyawan::find($token);

        if (! $karyawan || $karyawan->is_aktif) {
            return redirect()->route('aktivasi.invalid')->with('error', 'Akun tidak ditemukan atau sudah aktif.');
        }

        return view('aktivasi-form', [
            'karyawan' => $karyawan,
            'token' => $token,
        ]);
    }

    public function proses(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('aktivasi.invalid')->with('error', 'Link aktivasi tidak valid atau sudah kedaluwarsa.');
        }

        $karyawan = Karyawan::find($token);

        if (! $karyawan || $karyawan->is_aktif) {
            return redirect()->route('aktivasi.invalid')->with('error', 'Akun tidak ditemukan atau sudah aktif.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung huruf kapital, angka, dan karakter khusus.',
        ]);

        $karyawan->update([
            'password' => Hash::make($request->password),
            'is_aktif' => true,
        ]);

        return redirect()->guest(route('staff.login'))->with('success', 'Akun berhasil diaktifkan. Silakan login.');
    }

    public function invalid()
    {
        return view('aktivasi-invalid');
    }
}

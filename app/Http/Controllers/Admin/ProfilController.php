<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /**
     * GANTI NAMA KARYAWAN
     * Route: admin.profil.nama, kasir.profil.nama, operator.profil.nama
     */
    public function gantiNama(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'password.required' => 'Password wajib diisi untuk konfirmasi.',
        ]);

        // Ambil user yang login (karyawan)
        $user = Auth::guard('karyawan')->user();

        if (!$user) {
            return back()->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Verifikasi password
        if (!Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'Password tidak sesuai.'])
                ->with('modal_tab', 'nama');
        }

        // Update nama
        $user->update(['nama' => $validated['nama']]);

        // Tentukan route redirect berdasarkan role
        $role = $user->peran;
        $routeName = match($role) {
            'admin' => 'admin.dashboard',
            'kasir' => 'kasir.dashboard',
            'operator' => 'operator.dashboard',
            default => 'admin.dashboard',
        };

        return redirect()
            ->route($routeName)
            ->with('profil_sukses', 'Nama berhasil diubah!')
            ->with('modal_tab', 'nama');
    }

    /**
     * UBAH PASSWORD KARYAWAN
     * Route: admin.profil.password, kasir.profil.password, operator.profil.password
     */
    public function ubahPassword(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/', // Minimal 1 huruf besar
                'regex:/[0-9]/', // Minimal 1 angka
                'confirmed',
            ],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password minimal 8 karakter.',
            'password_baru.regex' => 'Password harus mengandung huruf besar dan angka.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Ambil user yang login
        $user = Auth::guard('karyawan')->user();

        if (!$user) {
            return back()->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Verifikasi password lama
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->with('modal_tab', 'password');
        }

        // Pastikan password baru berbeda dari password lama
        if (Hash::check($validated['password_baru'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password_baru' => 'Password baru harus berbeda dari password lama.'])
                ->with('modal_tab', 'password');
        }

        // Update password
        $user->update(['password' => Hash::make($validated['password_baru'])]);

        // Tentukan route redirect berdasarkan role
        $role = $user->peran;
        $routeName = match($role) {
            'admin' => 'admin.dashboard',
            'kasir' => 'kasir.dashboard',
            'operator' => 'operator.dashboard',
            default => 'admin.dashboard',
        };

        return redirect()
            ->route($routeName)
            ->with('profil_sukses', 'Password berhasil diubah!')
            ->with('modal_tab', 'password');
    }
}
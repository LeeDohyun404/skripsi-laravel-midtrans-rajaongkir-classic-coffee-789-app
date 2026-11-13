<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderComplaint;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil.
     * Menyediakan semua variabel yang dibutuhkan profile.blade.php
     */
    public function index()
    {
        $user = Auth::user();

        // ── Auto-proxy avatar Google ke DB kita (jika masih URL lh3.google) ──
        // Ini memperbaiki avatar tanpa perlu logout/login ulang
        if ($user->avatar && (str_contains($user->avatar, 'googleusercontent.com') || str_contains($user->avatar, 'lh3.google'))) {
            try {
                $proxied = \App\Http\Controllers\GoogleAuthController::downloadAndSaveAvatar($user->avatar);
                if ($proxied) {
                    $user->update(['avatar' => $proxied]);
                    $user->refresh();
                }
            } catch (\Throwable $e) {
                // Gagal proxy = tetap pakai URL Google, tidak masalah
            }
        }

        // ── Statistik pesanan ──────────────────────────────
        $orders = Order::where('customer_email', $user->email)
                       ->with(['items', 'trackings'])
                       ->latest()
                       ->get();

        $totalOrders   = $orders->count();
        $successOrders = $orders->filter(fn($o) =>
            in_array($o->status, ['success', 'settlement', 'capture', 'completed', 'claimed'])
        )->count();
        $pendingOrders = $orders->where('status', 'pending')->count();

        // ── Voucher aktif ──────────────────────────────────
        // Cek & expire voucher yang sudah lewat tanggal
        Voucher::where('user_id', $user->id)
               ->where('status', 'active')
               ->where('expires_at', '<=', now())
               ->update(['status' => 'expired']);

        $activeVoucher = Voucher::activeFor($user->id)->first();

        // Jika session voucher masih aktif tapi voucher sudah tidak valid, hapus session
        if (session('voucher_active') && ! $activeVoucher) {
            session()->forget(['voucher_active', 'voucher_id', 'voucher_code']);
        }

        // ── Riwayat voucher terpakai ───────────────────────
        $usedVouchers = Voucher::usedBy($user->id)->limit(5)->get();

        // ── Pengaduan pelanggan ───────────────────────────
        $complaints    = OrderComplaint::where('user_id', $user->id)
                                       ->with('order')
                                       ->latest()
                                       ->get();
        $totalPengaduan = $complaints->count();

        return view('profile', compact(
            'orders',
            'totalOrders',
            'successOrders',
            'pendingOrders',
            'activeVoucher',
            'usedVouchers',
            'complaints',
            'totalPengaduan',
        ));
    }

    /**
     * Update nama pengguna
     */
    public function updateName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Auth::user()->update(['name' => $request->name]);
        return back()->with('success', 'Nama berhasil diperbarui.');
    }

    /**
     * Update password
     * User yang login via Google (punya google_id) tidak boleh ubah password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // ── Blokir user Google agar tidak bisa ubah password ──
        if ($user->google_id) {
            return back()->with('error', 'Akun Google tidak bisa mengubah password. Password dikelola oleh Google.');
        }

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Update avatar / foto profil
     * Mendukung:
     *   1. multipart/form-data  (file upload biasa)
     *   2. application/json     { avatar_base64: "..." }  (dari canvas kompres JS)
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        // ── Mode 2: JSON base64 dari canvas kompres ──────────────────────
        if ($request->isJson() || $request->has('avatar_base64')) {
            $b64 = $request->input('avatar_base64');
            if (!$b64) {
                return response()->json(['error' => 'Data gambar kosong.'], 422);
            }

            try {
                $binaryData = base64_decode($b64, true);
                if (!$binaryData || strlen($binaryData) < 100) {
                    return response()->json(['error' => 'Data gambar tidak valid.'], 422);
                }

                // Simpan ke DB via ProductImageController
                $filename = 'av-' . time() . '-' . bin2hex(random_bytes(3)) . '.jpg';
                \App\Http\Controllers\ProductImageController::simpanRaw($filename, 'image/jpeg', $binaryData);

                $avatarUrl = url('/foto-avatar/' . $filename);
                $user->update(['avatar' => $avatarUrl]);

                return response()->json(['url' => $avatarUrl]);
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
            }
        }

        // ── Mode 1: file upload multipart biasa ──────────────────────────
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:4096']);

        $file = $request->file('avatar');

        // Simpan ke database via ProductImageController agar gambar persisten
        $filename = ProductImageController::simpan($file);

        // Simpan salinan ke storage disk jika memungkinkan
        try {
            $file->storeAs('avatars', $filename, 'public');
        } catch (\Throwable $e) {}

        $avatarUrl = url('/foto-avatar/' . $filename);
        $user->update(['avatar' => $avatarUrl]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
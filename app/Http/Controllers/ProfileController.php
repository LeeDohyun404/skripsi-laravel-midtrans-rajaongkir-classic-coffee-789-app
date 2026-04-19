<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil.
     * Menyediakan semua variabel yang dibutuhkan profile.blade.php
     */
    public function index()
    {
        $user = Auth::user();

        // ── Statistik pesanan ──────────────────────────────
        $orders = Order::where('customer_email', $user->email)
                       ->with('items')
                       ->latest()
                       ->get();

        $totalOrders   = $orders->count();
        $successOrders = $orders->filter(fn($o) =>
            in_array($o->status, ['success', 'settlement', 'capture'])
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

        return view('profile', compact(
            'orders',
            'totalOrders',
            'successOrders',
            'pendingOrders',
            'activeVoucher',
            'usedVouchers',
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
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Update avatar / foto profil
     */
    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048']);

        $user = Auth::user();

        // Hapus avatar lama jika bukan dari Google
        if ($user->avatar && ! str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => Storage::url($path)]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
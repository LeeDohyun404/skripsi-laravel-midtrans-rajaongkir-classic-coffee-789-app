<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderComplaint;
use App\Models\Order;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'jenis'    => 'required|in:barang_rusak,tidak_sesuai,tidak_diterima,terlambat,lainnya',
            'detail'   => 'required|string|min:10|max:1000',
            'foto'     => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'order_id.required' => 'Pilih pesanan terlebih dahulu.',
            'order_id.exists'   => 'Pesanan tidak ditemukan.',
            'jenis.required'    => 'Pilih jenis pengaduan.',
            'detail.required'   => 'Detail pengaduan wajib diisi.',
            'detail.min'        => 'Detail pengaduan minimal 10 karakter.',
            'foto.required'     => 'Foto bukti wajib diunggah.',
            'foto.image'        => 'File harus berupa gambar.',
            'foto.max'          => 'Ukuran foto maksimal 2MB.',
        ]);

        $user  = Auth::user();

        // Pastikan order milik pelanggan ini
        $order = Order::where('id', $request->order_id)
                      ->where('customer_email', $user->email)
                      ->firstOrFail();

        // Upload foto
        $fotoPath = $request->file('foto')->store('complaints', 'public');

        OrderComplaint::create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'jenis'    => $request->jenis,
            'detail'   => $request->detail,
            'foto'     => $fotoPath,
            'status'   => 'menunggu',
        ]);

        return back()->with('success', '✅ Pengaduan berhasil dikirim! Tim kami akan segera menangani laporan kamu.')->withFragment('panel-pengaduan');
    }
}
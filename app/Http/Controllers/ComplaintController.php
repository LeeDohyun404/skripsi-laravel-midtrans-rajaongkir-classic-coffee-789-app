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
            'order_id'    => 'required|exists:orders,id',
            'jenis'       => 'required|in:barang_rusak,tidak_sesuai,tidak_diterima,terlambat,lainnya',
            'detail'      => 'required|string|min:10|max:1000',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'foto_base64' => 'nullable|string',
        ], [
            'order_id.required' => 'Pilih pesanan terlebih dahulu.',
            'order_id.exists'   => 'Pesanan tidak ditemukan.',
            'jenis.required'    => 'Pilih jenis pengaduan.',
            'detail.required'   => 'Detail pengaduan wajib diisi.',
            'detail.min'        => 'Detail pengaduan minimal 10 karakter.',
        ]);

        $user  = Auth::user();

        // Pastikan order milik pelanggan ini
        $order = Order::where('id', $request->order_id)
                      ->where('customer_email', $user->email)
                      ->firstOrFail();

        $filename = null;

        // Mode 1: Jika dikirim via Canvas base64 kompresi
        if ($request->filled('foto_base64')) {
            $b64 = $request->input('foto_base64');
            $binary = base64_decode($b64, true);
            if ($binary && strlen($binary) > 50) {
                $filename = 'bukti-' . time() . '-' . bin2hex(random_bytes(3)) . '.jpg';
                \App\Http\Controllers\ProductImageController::simpanRaw($filename, 'image/jpeg', $binary);
            }
        }

        // Mode 2: Jika upload multipart file biasa
        if (!$filename && $request->hasFile('foto')) {
            $filename = \App\Http\Controllers\ProductImageController::simpan($request->file('foto'));
        }

        if (!$filename) {
            return back()->withErrors(['foto' => 'Foto bukti wajib diunggah.'])->withInput();
        }

        OrderComplaint::create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'jenis'    => $request->jenis,
            'detail'   => $request->detail,
            'foto'     => $filename,
            'status'   => 'menunggu',
        ]);

        return back()->with('success', '✅ Pengaduan berhasil dikirim! Tim kami akan segera menangani laporan kamu.')->withFragment('panel-pengaduan');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller 
{
    // 1. Fungsi buat daftar produk
    public function index()
    {
        $products = DB::table('products')->get();
        return view('admin.index', ['products' => $products]);
    }

    // 2. Fungsi buat riwayat pesanan
    public function history()
    {
        $orders = DB::table('orders')->latest()->get();
        return view('admin.history', compact('orders'));
    }

    // 3. FUNGSI BARU: Buat Kelola Pelanggan
    public function customers()
    {
        // Kita ambil data dari tabel 'users'
        // latest() biar yang baru daftar muncul di atas
        $customers = DB::table('users')->latest()->get();
        
        return view('admin.pelanggan', compact('customers'));
    }
}
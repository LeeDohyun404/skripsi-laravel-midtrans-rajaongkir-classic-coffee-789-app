<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;

class AdminController extends Controller
{
    // ✅ PERBAIKAN: Method dashboard dengan data lengkap untuk blade
    public function dashboard()
    {
        $totalKaryawan      = Karyawan::count();
        $karyawanAktif      = Karyawan::where('is_aktif', true)->whereNotNull('password')->count();
        $karyawanBelumAktif = Karyawan::where(function ($q) {
                                    $q->where('is_aktif', false)->orWhereNull('password');
                                })->count();
        $totalPelanggan     = DB::table('users')->count();
        $transaksiHariIni   = DB::table('orders')->whereDate('created_at', today())->count();
        $karyawanTerbaru    = Karyawan::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'karyawanAktif',
            'karyawanBelumAktif',
            'totalPelanggan',
            'transaksiHariIni',
            'karyawanTerbaru'
        ));
    }

    public function index()
    {
        $products = DB::table('products')->get();
        return view('admin.karyawan.index', ['products' => $products]);
    }

    public function history()
    {
        $orders = DB::table('orders')->latest()->get();
        return view('admin.history', compact('orders'));
    }

    public function customers()
    {
        $customers         = DB::table('users')->latest()->get();
        $pelangganBulanIni = DB::table('users')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $pelangganHariIni  = DB::table('users')->whereDate('created_at', today())->count();
        return view('admin.pelanggan', compact('customers', 'pelangganBulanIni', 'pelangganHariIni'));
    }

    public function destroyPelanggan($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus!');
    }

    public function stok()
    {
        $products    = DB::table('products')->get();
        $totalProduk = DB::table('products')->count();
        $stokHabis   = DB::table('products')->where('stock', '<=', 0)->count();
        $stokRendah  = DB::table('products')->whereBetween('stock', [1, 10])->count();
        $stokAman    = DB::table('products')->where('stock', '>', 10)->count();
        return view('admin.stok', compact('products', 'totalProduk', 'stokHabis', 'stokRendah', 'stokAman'));
    }
}
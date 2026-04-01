<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller 
{
    public function index()
    {
        $products = DB::table('products')->get();
        return view('admin.index', ['products' => $products]);
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
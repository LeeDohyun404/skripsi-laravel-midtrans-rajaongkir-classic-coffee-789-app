<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $customers = User::latest()->get();

        $pelangganBulanIni = User::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count();

        $pelangganHariIni = User::whereDate('created_at', today())->count();

        return view('admin.pelanggan', compact('customers', 'pelangganBulanIni', 'pelangganHariIni'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.pelanggan')
                         ->with('success', 'Pelanggan berhasil dihapus!');
    }
}
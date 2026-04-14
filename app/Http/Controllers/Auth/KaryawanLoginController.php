<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanLoginController extends Controller
{
    // --- FITUR LOGIN ---
    public function showLoginForm() {
        return view('auth.login-karyawan');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('karyawan')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('karyawan')->user();

            $user->update(['last_login_at' => now()]);

            if ($user->peran === 'admin') return redirect()->intended('/admin/karyawan');
            if ($user->peran === 'operator') return redirect()->intended('/operator/stok');
            return redirect()->intended('/kasir/transaksi');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    // --- MANAJEMEN KARYAWAN (CRUD) ---

    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($request->filled('peran')) {
            $query->where('peran', $request->peran);
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status);
        }

        $karyawan = $query->paginate(10);

        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function create() {
        return view('admin.karyawan.tambah');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nama'     => 'required|max:100',
            'username' => 'required|unique:karyawan,username|max:50',
            'password' => 'required|min:8|confirmed',
            'peran'    => 'required',
            'email'    => 'nullable|email',
            'foto'     => 'nullable|image|max:1024',
        ]);

        $data['password'] = Hash::make($request->password);
        $data['is_aktif'] = $request->has('is_aktif') ? 1 : 0;
        $data['shift']    = $request->shift;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        Karyawan::create($data);
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambah!');
    }

    public function edit($id) {
        $karyawan = Karyawan::findOrFail($id);
        return view('admin.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $id) {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama'     => 'required',
            'username' => 'required|unique:karyawan,username,' . $id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $karyawan->nama     = $request->nama;
        $karyawan->username = $request->username;
        $karyawan->peran    = $request->peran;
        $karyawan->shift    = $request->shift;
        $karyawan->is_aktif = $request->has('is_aktif') ? 1 : 0;

        if ($request->filled('password')) {
            $karyawan->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) Storage::disk('public')->delete($karyawan->foto);
            $karyawan->foto = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan->save();
        return redirect()->route('admin.karyawan.index')->with('success', 'Data berhasil diubah!');
    }

    public function destroy($id) {
        $karyawan = Karyawan::findOrFail($id);
        if ($karyawan->foto) Storage::disk('public')->delete($karyawan->foto);
        $karyawan->forceDelete();
        return back()->with('success', 'Karyawan berhasil dihapus!');
    }

    public function logout() {
        Auth::guard('karyawan')->logout();
        return redirect('/login-staff');
    }
}
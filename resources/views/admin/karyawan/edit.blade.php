@extends('layouts.karyawan')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('admin.dashboard') }}" class="nav-item">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Manajemen</span>
    <a href="{{ route('admin.karyawan.index') }}" class="nav-item active">
        <span class="nav-icon">👥</span> Karyawan
    </a>
    <a href="{{ route('admin.pelanggan') }}" class="nav-item">
        <span class="nav-icon">🧑‍🤝‍🧑</span> Pelanggan
    </a>
    <a href="{{ route('admin.stok') }}" class="nav-item">
        <span class="nav-icon">📦</span> Stok
    </a>
    <a href="{{ route('admin.history') }}" class="nav-item">
        <span class="nav-icon">📋</span> History Transaksi
    </a>
@endsection

@section('content')

{{-- Status badge --}}
<div style="margin-bottom:20px;display:flex;align-items:center;gap:12px;">
    <a href="{{ route('admin.karyawan.index') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">← Kembali ke Daftar</a>
    <span style="color:var(--cream-dark);">|</span>
    @if ($karyawan->sudahAktif())
        <span class="badge badge-green">✓ Akun Aktif</span>
    @elseif ($karyawan->tokenMasihValid())
        <span class="badge badge-gold">📧 Undangan Dikirim</span>
    @else
        <span class="badge badge-red">✗ Link Expired</span>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

    {{-- Main form --}}
    <div class="card">
        <div class="card-header">
            <h2>Edit Data Karyawan</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.karyawan.update', $karyawan->id) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">

                    <div class="form-group full">
                        <label for="nama">Nama Lengkap <span style="color:#c0392b;">*</span></label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old('nama', $karyawan->nama) }}"
                            required
                        >
                        @error('nama')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $karyawan->email }}"
                            disabled
                            style="background:var(--cream-dark);color:var(--text-muted);cursor:not-allowed;"
                        >
                        <span style="font-size:12px;color:var(--text-muted);">
                            Email tidak bisa diubah setelah karyawan dibuat.
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input
                            type="tel"
                            id="no_hp"
                            name="no_hp"
                            value="{{ old('no_hp', $karyawan->no_hp) }}"
                            placeholder="08xx-xxxx-xxxx"
                        >
                        @error('no_hp')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="peran">Peran <span style="color:#c0392b;">*</span></label>
                        <select id="peran" name="peran" required>
                            <option value="admin"    {{ old('peran', $karyawan->peran) === 'admin'    ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('peran', $karyawan->peran) === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="kasir"    {{ old('peran', $karyawan->peran) === 'kasir'    ? 'selected' : '' }}>Kasir</option>
                        </select>
                        @error('peran')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" style="resize:vertical;">{{ old('alamat', $karyawan->alamat) }}</textarea>
                        @error('alamat')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div style="display:flex;gap:12px;margin-top:28px;padding-top:20px;border-top:1px solid var(--cream-dark);">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Sidebar info --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Info card --}}
        <div class="card">
            <div class="card-header"><h2>Info Akun</h2></div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--text-muted);line-height:2;">
                    <div><strong style="color:var(--text);">Dibuat</strong><br>{{ $karyawan->created_at->format('d M Y, H:i') }}</div>
                    <div style="margin-top:12px;"><strong style="color:var(--text);">Aktivasi</strong><br>
                        {{ $karyawan->activated_at ? $karyawan->activated_at->format('d M Y, H:i') : '—' }}
                    </div>
                    <div style="margin-top:12px;"><strong style="color:var(--text);">Status</strong><br>
                        @if ($karyawan->sudahAktif())
                            <span style="color:#2e7d52;">✓ Aktif</span>
                        @elseif ($karyawan->tokenMasihValid())
                            <span style="color:#8a6520;">📧 Menunggu Aktivasi</span>
                        @else
                            <span style="color:#c0392b;">✗ Link Expired</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Resend undangan --}}
        @if (!$karyawan->sudahAktif())
            <div class="card">
                <div class="card-body">
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;line-height:1.7;">
                        Karyawan belum mengaktifkan akunnya. Kirim ulang email undangan jika link sebelumnya sudah expired.
                    </div>
                    <form method="POST" action="{{ route('admin.karyawan.resend', $karyawan->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="width:100%;"
                            onclick="return confirm('Kirim ulang undangan ke {{ $karyawan->email }}?')">
                            📧 Kirim Ulang Undangan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Danger zone --}}
        <div class="card" style="border-color:#fdecea;">
            <div class="card-header" style="border-color:#fdecea;">
                <h2 style="color:#c0392b;font-size:14px;">Zona Berbahaya</h2>
            </div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">
                    Hapus karyawan ini secara permanen. Tindakan tidak bisa dibatalkan.
                </div>
                <form method="POST" action="{{ route('admin.karyawan.destroy', $karyawan->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;"
                        onclick="return confirm('Yakin hapus karyawan {{ $karyawan->nama }}? Tindakan ini PERMANEN.')">
                        Hapus Karyawan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

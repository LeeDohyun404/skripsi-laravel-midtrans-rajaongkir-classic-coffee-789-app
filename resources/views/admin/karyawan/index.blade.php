@extends('layouts.karyawan')

@section('title', 'Manajemen Karyawan')
@section('page-title', 'Manajemen Karyawan')

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

@if (session('warning'))
    <div class="card" style="margin-bottom:16px;border-left:4px solid #d4a017;">
        <div style="padding:16px 20px;">
            <div style="font-weight:600;color:#7a5c00;margin-bottom:8px;">⚠️ {{ session('warning') }}</div>

            @if (session('manual_activation_url'))
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:10px;">
                    Email otomatis gagal, tetapi link aktivasi manual sudah dibuat dan masih bisa dipakai.
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                    <input
                        type="text"
                        value="{{ session('manual_activation_url') }}"
                        id="manualActivationUrl"
                        readonly
                        style="flex:1;min-width:280px;padding:10px 12px;border:1.5px solid var(--cream-dark);border-radius:8px;font-size:12px;background:#fff;"
                    >
                    <button
                        type="button"
                        class="btn btn-outline btn-sm"
                        onclick="copyManualActivationUrl()">
                        Salin Link
                    </button>
                    <a
                        href="{{ session('manual_activation_url') }}"
                        target="_blank"
                        rel="noopener"
                        class="btn btn-primary btn-sm">
                        Buka Link
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h2>Daftar Karyawan</h2>
        <a href="{{ route('admin.karyawan.tambah') }}" class="btn btn-primary">
            + Tambah Karyawan
        </a>
    </div>

    {{-- Filter bar --}}
    <div style="padding:16px 24px;border-bottom:1px solid var(--cream-dark);display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.karyawan.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;width:100%;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau email..."
                style="flex:1;min-width:200px;padding:8px 14px;border:1.5px solid var(--cream-dark);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;"
            >
            <select name="peran" style="padding:8px 14px;border:1.5px solid var(--cream-dark);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;color:var(--text);">
                <option value="">Semua Peran</option>
                <option value="admin"    {{ request('peran') === 'admin'    ? 'selected' : '' }}>Admin</option>
                <option value="operator" {{ request('peran') === 'operator' ? 'selected' : '' }}>Operator</option>
                <option value="kasir"    {{ request('peran') === 'kasir'    ? 'selected' : '' }}>Kasir</option>
            </select>
            <select name="status" style="padding:8px 14px;border:1.5px solid var(--cream-dark);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;color:var(--text);">
                <option value="">Semua Status</option>
                <option value="aktif"       {{ request('status') === 'aktif'       ? 'selected' : '' }}>Aktif</option>
                <option value="belum_aktif" {{ request('status') === 'belum_aktif' ? 'selected' : '' }}>Belum Aktivasi</option>
            </select>
            <button type="submit" class="btn btn-outline">Filter</button>
            @if (request()->hasAny(['search','peran','status']))
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status Aktivasi</th>
                    <th>Bergabung</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $i => $k)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;">{{ $karyawan->firstItem() + $i }}</td>
                        <td style="font-weight:500;">{{ $k->nama }}</td>
                        <td style="color:var(--text-muted);">{{ $k->email }}</td>
                        <td>
                            <span class="badge
                                {{ $k->peran === 'admin'    ? 'badge-gold'  : '' }}
                                {{ $k->peran === 'operator' ? 'badge-green' : '' }}
                                {{ $k->peran === 'kasir'    ? 'badge-blue'  : '' }}
                            ">
                                {{ ucfirst($k->peran) }}
                            </span>
                        </td>
                        <td>
                            @if ($k->sudahAktif())
                                <span class="badge badge-green">✓ Aktif</span>
                            @elseif ($k->tokenMasihValid())
                                <span class="badge badge-gold">📧 Diundang</span>
                            @else
                                <span class="badge badge-red">✗ Link Expired</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:12px;">
                            {{ $k->created_at->format('d M Y') }}
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                                <a href="{{ route('admin.karyawan.edit', $k->id) }}" class="btn btn-outline btn-sm">Edit</a>

                                {{-- Resend undangan --}}
                                @if (!$k->sudahAktif())
                                    <form method="POST" action="{{ route('admin.karyawan.resend', $k->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm"
                                            onclick="return confirm('Kirim ulang undangan ke {{ $k->email }}?')"
                                            title="Kirim ulang email undangan">
                                            📧 Resend
                                        </button>
                                    </form>
                                @endif

                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('admin.karyawan.destroy', $k->id) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus karyawan {{ $k->nama }}? Tindakan ini tidak bisa dibatalkan.')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-muted);padding:48px;">
                            <div style="font-size:32px;margin-bottom:12px;">👥</div>
                            Belum ada karyawan.
                            <a href="{{ route('admin.karyawan.tambah') }}" style="color:var(--brown-warm);">Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($karyawan->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--cream-dark);">
            {{ $karyawan->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
    function copyManualActivationUrl() {
        const input = document.getElementById('manualActivationUrl');

        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Link aktivasi berhasil disalin.');
        }).catch(() => {
            document.execCommand('copy');
            alert('Link aktivasi berhasil disalin.');
        });
    }
</script>
@endsection

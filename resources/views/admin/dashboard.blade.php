@extends('layouts.karyawan')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Dashboard
    </a>

    <span class="nav-section-label">Manajemen</span>
    <a href="{{ route('admin.karyawan.index') }}" class="nav-item {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
        <span class="nav-icon">👥</span> Karyawan
    </a>
    <a href="{{ route('admin.pelanggan') }}" class="nav-item {{ request()->routeIs('admin.pelanggan') ? 'active' : '' }}">
        <span class="nav-icon">🧑‍🤝‍🧑</span> Pelanggan
    </a>
    <a href="{{ route('admin.stok') }}" class="nav-item {{ request()->routeIs('admin.stok') ? 'active' : '' }}">
        <span class="nav-icon">📦</span> Stok
    </a>
    <a href="{{ route('admin.history') }}" class="nav-item {{ request()->routeIs('admin.history') ? 'active' : '' }}">
        <span class="nav-icon">📋</span> History Transaksi
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ $totalKaryawan ?? 0 }}</div>
        <div class="stat-label">Total Karyawan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $karyawanAktif ?? 0 }}</div>
        <div class="stat-label">Karyawan Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value">{{ $karyawanBelumAktif ?? 0 }}</div>
        <div class="stat-label">Belum Aktivasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧑‍🤝‍🧑</div>
        <div class="stat-value">{{ $totalPelanggan ?? 0 }}</div>
        <div class="stat-label">Total Pelanggan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">{{ $transaksiHariIni ?? 0 }}</div>
        <div class="stat-label">Transaksi Hari Ini</div>
    </div>
</div>

{{-- Karyawan terbaru --}}
<div class="card">
    <div class="card-header">
        <h2>Karyawan Terbaru</h2>
        <a href="{{ route('admin.karyawan.tambah') }}" class="btn btn-primary btn-sm">+ Tambah Karyawan</a>
    </div>
    <div class="card-body" style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawanTerbaru ?? [] as $k)
                    <tr>
                        <td>{{ $k->nama }}</td>
                        <td style="color:var(--text-muted);">{{ $k->email }}</td>
                        <td>
                            <span class="badge {{ $k->peran === 'admin' ? 'badge-gold' : ($k->peran === 'kasir' ? 'badge-blue' : 'badge-green') }}">
                                {{ ucfirst($k->peran) }}
                            </span>
                        </td>
                        <td>
                            @if ($k->sudahAktif())
                                <span class="badge badge-green">✓ Aktif</span>
                            @else
                                <span class="badge badge-gray">⏳ Belum Aktivasi</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:12px;">
                            {{ $k->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">
                            Belum ada karyawan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

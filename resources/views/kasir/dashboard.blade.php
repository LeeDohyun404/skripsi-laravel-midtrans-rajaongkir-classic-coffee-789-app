@extends('layouts.karyawan')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('kasir.dashboard') }}" class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Transaksi</span>
    <a href="{{ route('kasir.transaksi') }}" class="nav-item {{ request()->routeIs('kasir.transaksi') ? 'active' : '' }}">
        <span class="nav-icon">🧾</span> Buat Transaksi
    </a>
@endsection

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value">{{ $transaksiHariIni ?? 0 }}</div>
        <div class="stat-label">Transaksi Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
        <div class="stat-label">Pendapatan Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value">{{ $transaksiMingguIni ?? 0 }}</div>
        <div class="stat-label">Transaksi Minggu Ini</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Transaksi Terbaru Hari Ini</h2>
        <a href="{{ route('kasir.transaksi') }}" class="btn btn-primary btn-sm">+ Transaksi Baru</a>
    </div>
    <div style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Metode Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerbaru ?? [] as $t)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;">
                            {{ $t->created_at->format('H:i') }}
                        </td>
                        <td>{{ $t->pelanggan->nama ?? 'Umum' }}</td>
                        <td style="font-weight:500;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td style="color:var(--text-muted);">{{ ucfirst($t->metode_bayar ?? '—') }}</td>
                        <td>
                            <span class="badge badge-green">✓ Selesai</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">
                            Belum ada transaksi hari ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

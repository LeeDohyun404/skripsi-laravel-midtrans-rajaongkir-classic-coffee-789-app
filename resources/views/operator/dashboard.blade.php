@extends('layouts.karyawan')

@section('title', 'Dashboard Operator')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('operator.dashboard') }}" class="nav-item {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Inventori</span>
    <a href="{{ route('operator.stok') }}" class="nav-item {{ request()->routeIs('operator.stok') ? 'active' : '' }}">
        <span class="nav-icon">📦</span> Manajemen Stok
    </a>
@endsection

@section('content')

@if($errors->any())
    <div style="background:#fdecea;border:1px solid #f5c0bc;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#c0392b;font-size:13px;">
        @foreach($errors->all() as $e)
            <p>⚠️ {{ $e }}</p>
        @endforeach
    </div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-value">{{ $totalProduk ?? 0 }}</div>
        <div class="stat-label">Total Produk</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value">{{ $stokRendah ?? 0 }}</div>
        <div class="stat-label">Stok Hampir Habis</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $stokAman ?? 0 }}</div>
        <div class="stat-label">Stok Aman</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Stok Hampir Habis</h2>
        <a href="{{ route('operator.stok') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stokHampirHabis ?? [] as $s)
                    <tr>
                        <td style="font-weight:500;">{{ $s->nama_produk }}</td>
                        <td style="color:var(--text-muted);">{{ $s->kategori }}</td>
                        <td>{{ $s->stok }} {{ $s->satuan }}</td>
                        <td>
                            <span class="badge badge-red">⚠️ Hampir Habis</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--text-muted);padding:32px;">
                            ✅ Semua stok dalam kondisi aman
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

{{-- ✅ FIX: Pindah ke @push('scripts') supaya dimuat SETELAH layout selesai render --}}
@push('scripts')
    @include('admin.modal_profil')
@endpush
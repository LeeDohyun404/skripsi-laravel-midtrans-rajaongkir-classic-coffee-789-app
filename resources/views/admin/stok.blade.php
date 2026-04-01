{{-- resources/views/admin/stok.blade.php --}}
@extends('layouts.app')
@section('title', 'Lihat Stok Produk')
@section('content')

{{-- Navbar Staff --}}
<nav class="mb-4 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#1c1917;">
    <span class="fw-black text-white fs-5">
        <i class="bi bi-cup-hot-fill text-warning me-2"></i>
        CLASSIC<span class="text-warning">789</span> Staff
    </span>
    <div class="d-flex align-items-center gap-2">
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/karyawan*') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/karyawan"><i class="bi bi-people-fill me-1"></i> Kelola Karyawan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/pelanggan') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/pelanggan"><i class="bi bi-person-lines-fill me-1"></i> Kelola Pelanggan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/history') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/history"><i class="bi bi-clock-history me-1"></i> History Pesanan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/stok') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/stok"><i class="bi bi-box-seam me-1"></i> Lihat Stok Produk</a>
        <form method="POST" action="{{ route('staff.logout') }}" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>


<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Stok Produk</h4>
        <span class="badge bg-secondary">Mode: Hanya Lihat</span>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-primary fs-2 fw-bold">{{ $totalProduk }}</div>
                <div class="text-muted small">Total Produk</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-success fs-2 fw-bold">{{ $stokAman }}</div>
                <div class="text-muted small">Stok Aman</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-warning fs-2 fw-bold">{{ $stokRendah }}</div>
                <div class="text-muted small">Stok Rendah</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-danger fs-2 fw-bold">{{ $stokHabis }}</div>
                <div class="text-muted small">Stok Habis</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('images/'.$p->image_url) }}" width="40" height="40"
                                         style="object-fit:cover;border-radius:6px"
                                         onerror="this.src='https://placehold.co/40x40?text=?'">
                                    <span class="fw-semibold">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->category ?? '-' }}</span></td>
                            <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="fw-bold {{ $p->stock <= 0 ? 'text-danger' : ($p->stock <= 10 ? 'text-warning' : 'text-success') }}">
                                    {{ $p->stock ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if(($p->stock ?? 0) <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($p->stock <= 10)
                                    <span class="badge bg-warning text-dark">Rendah</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>Belum ada produk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
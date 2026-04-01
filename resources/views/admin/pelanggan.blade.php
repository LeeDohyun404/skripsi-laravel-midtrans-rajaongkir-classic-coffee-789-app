{{-- resources/views/admin/pelanggan.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Pelanggan')

@section('content')

{{-- Navbar Staff --}}
<nav class="mb-4 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#1c1917;">
    <span class="fw-black text-white fs-5">
        <i class="bi bi-cup-hot-fill text-warning me-2"></i>
        CLASSIC<span class="text-warning">789</span> Staff
    </span>
    <div class="d-flex align-items-center gap-2">
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/karyawan*') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/karyawan">
            <i class="bi bi-people-fill me-1"></i> Kelola Karyawan
        </a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/pelanggan') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/pelanggan">
            <i class="bi bi-person-lines-fill me-1"></i> Kelola Pelanggan
        </a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/history') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/history">
            <i class="bi bi-clock-history me-1"></i> History Pesanan
        </a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small text-white"
           href="/operator/stok">
            <i class="bi bi-box-seam me-1"></i> Lihat Stok Produk
        </a>
        <form method="POST" action="{{ route('staff.logout') }}" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-lines-fill me-2 text-primary"></i>Kelola Pelanggan
        </h4>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-primary fs-2 fw-bold">{{ count($customers) }}</div>
                <div class="text-muted small">Total Pelanggan</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-success fs-2 fw-bold">{{ $pelangganBulanIni }}</div>
                <div class="text-muted small">Bergabung Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-warning fs-2 fw-bold">{{ $pelangganHariIni }}</div>
                <div class="text-muted small">Bergabung Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Login Via</th>
                            <th>Tanggal Daftar</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($c->avatar)
                                        <img src="{{ $c->avatar }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:14px">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="fw-semibold">{{ $c->name }}</span>
                                </div>
                            </td>
                            <td>{{ $c->email ?? '-' }}</td>
                            <td>
                                @if($c->google_id)
                                    <span class="badge bg-danger"><i class="bi bi-google me-1"></i> Google</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-envelope me-1"></i> Email</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        title="Detail"
                                        data-nama="{{ $c->name }}"
                                        data-email="{{ $c->email }}"
                                        data-login="{{ $c->google_id ? 'Google' : 'Email' }}"
                                        data-join="{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y H:i') }}"
                                        onclick="lihatDetail(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <form method="POST" action="/admin/pelanggan/{{ $c->id }}/hapus" style="display:inline"
                                      onsubmit="return confirm('Hapus pelanggan {{ addslashes($c->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada pelanggan terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Detail Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr><th>Nama</th><td id="detailNama"></td></tr>
                    <tr><th>Email</th><td id="detailEmail"></td></tr>
                    <tr><th>Login Via</th><td id="detailLogin"></td></tr>
                    <tr><th>Bergabung</th><td id="detailJoin"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function lihatDetail(btn) {
    document.getElementById('detailNama').textContent  = btn.dataset.nama;
    document.getElementById('detailEmail').textContent = btn.dataset.email;
    document.getElementById('detailLogin').textContent = btn.dataset.login;
    document.getElementById('detailJoin').textContent  = btn.dataset.join;
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}
</script>

@endsection
{{-- resources/views/admin/karyawan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')

{{-- Navbar Staff (sama persis strukturnya dengan pelanggan.blade.php) --}}
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
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/pelanggan*') ? 'bg-warning text-dark' : 'text-white' }}"
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
            <i class="bi bi-people-fill me-2 text-primary"></i>Manajemen Karyawan
        </h4>
        <a href="{{ route('admin.karyawan.tambah') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Karyawan
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Peran --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label fw-semibold">Filter Peran:</label>
                </div>
                <div class="col-auto">
                    <select name="peran" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Peran</option>
                        <option value="admin"    {{ request('peran') == 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="operator" {{ request('peran') == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="kasir"    {{ request('peran') == 'kasir'    ? 'selected' : '' }}>Kasir</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </form>
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
                            <th>Username</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Login Terakhir</th>
                            <th width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawan as $i => $k)
                        <tr>
                            <td>{{ $karyawan->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($k->foto)
                                        <img src="{{ asset('storage/'.$k->foto) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px">
                                            {{ strtoupper(substr($k->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $k->nama }}</span>
                                </div>
                            </td>
                            <td><code>{{ $k->username }}</code></td>
                            <td>{{ $k->email ?? '-' }}</td>
                            <td>
                              @php
                                $badgeClass = match($k->peran) {
                                    'admin'    => 'bg-danger',
                                    'operator' => 'bg-primary',
                                    'kasir'    => 'bg-success',
                                    default    => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($k->peran ?? '-') }}</span>
                            </td>
                            <td>{{ $k->shift ?? '-' }}</td>
                            <td>
                                @if($k->is_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $k->last_login_at?->diffForHumans() ?? 'Belum pernah' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.karyawan.edit', $k->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus"
                                        onclick="konfirmasiHapus({{ $k->id }}, '{{ $k->nama }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada data karyawan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $karyawan->links() }}
    </div>

</div>

{{-- Form Hapus --}}
<form id="formHapus" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function konfirmasiHapus(id, nama) {
    if (confirm(`Hapus karyawan "${nama}"? Tindakan ini tidak bisa dibatalkan.`)) {
        const form = document.getElementById('formHapus');
        form.action = `/admin/karyawan/${id}`;
        form.submit();
    }
}
</script>

@endsection
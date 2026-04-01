{{-- resources/views/operator/stok.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Stok')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-box-seam me-2 text-primary"></i>Kelola Stok & Produk
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="bi bi-plus-circle me-1"></i> Tambah Produk
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    {{-- Filter & Search --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm" placeholder="Cari nama produk...">
                </div>
                <div class="col-auto">
                    <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="stok_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Stok</option>
                        <option value="aman"   {{ request('stok_filter') == 'aman'   ? 'selected' : '' }}>Stok Aman</option>
                        <option value="rendah" {{ request('stok_filter') == 'rendah' ? 'selected' : '' }}>Stok Rendah</option>
                        <option value="habis"  {{ request('stok_filter') == 'habis'  ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Produk --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $i => $p)
                        <tr>
                            <td>{{ $products->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($p->image_url)
                                        <img src="{{ asset('images/'.$p->image_url) }}" width="40" height="40"
                                             style="object-fit:cover;border-radius:6px">
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                             style="width:40px;height:40px;font-size:18px">
                                            <i class="bi bi-box text-white"></i>
                                        </div>
                                    @endif
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
                            <td>
                                <button class="btn btn-sm btn-outline-success"
                                        onclick="modalUpdateStok({{ $p->id }}, '{{ $p->name }}', {{ $p->stock ?? 0 }})"
                                        title="Update Stok">
                                    <i class="bi bi-arrow-repeat"></i> Stok
                                </button>
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="modalEditProduk({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, '{{ $p->category }}')"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('operator.produk.hapus', $p->id) }}"
                                    style="display:inline"
                                    onsubmit="return confirm('Hapus produk {{ addslashes($p->name) }}?')">
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada produk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>

</div>

{{-- Modal Tambah Produk --}}
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('operator.produk.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" class="form-control" required min="0">
                        </div>
                    </div>
                    {{-- SESUDAH --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select name="category" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok Awal</label>
                        <input type="number" name="stock" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Produk</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Update Stok --}}
<div class="modal fade" id="modalStok" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('operator.stok.update') }}">
                @csrf
                <input type="hidden" name="product_id" id="stokProductId">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-2">Produk: <strong id="stokNamaProduk"></strong></p>
                    <label class="form-label fw-semibold">Stok Baru</label>
                    <input type="number" name="stock" id="stokJumlah" class="form-control" min="0" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Produk --}}
<div class="modal fade" id="modalEditProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('operator.produk.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editProdukId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="name" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" id="editHarga" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <input type="text" name="category" id="editKategori" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function modalUpdateStok(id, nama, stokSaat) {
    document.getElementById('stokProductId').value = id;
    document.getElementById('stokNamaProduk').textContent = nama;
    document.getElementById('stokJumlah').value = stokSaat;
    new bootstrap.Modal(document.getElementById('modalStok')).show();
}

function modalEditProduk(id, nama, harga, kategori) {
    document.getElementById('editProdukId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editHarga').value = harga;
    document.getElementById('editKategori').value = kategori;
    new bootstrap.Modal(document.getElementById('modalEditProduk')).show();
}
</script>

@endsection
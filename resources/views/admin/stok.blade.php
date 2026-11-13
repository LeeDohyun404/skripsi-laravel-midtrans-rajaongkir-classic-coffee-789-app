@extends('layouts.karyawan')

@section('title', 'Manajemen Stok - Admin')
@section('page-title', 'Stok Produk')

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

@push('styles')
    <style>
        .page-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .page-toolbar p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .panel-card {
            background: white;
            border: 1px solid var(--cream-dark);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(61, 36, 16, 0.08);
            overflow: hidden;
        }

        .panel-card + .panel-card {
            margin-top: 18px;
        }

        .panel-card-body {
            padding: 20px 22px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .field-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .form-control-modern,
        .form-select-modern,
        .form-file-modern {
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            border: 1px solid var(--cream-dark);
            background: #fff;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
        }

        .form-control-modern:focus,
        .form-select-modern:focus,
        .form-file-modern:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.14);
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-thumb {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--cream-dark);
            flex-shrink: 0;
        }

        .product-fallback {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f5ead8;
            color: var(--brown-mid);
            border: 1px solid var(--cream-dark);
            flex-shrink: 0;
            font-size: 18px;
        }

        .price-text {
            font-family: 'Playfair Display', serif;
            color: var(--brown-mid);
            white-space: nowrap;
        }

        .stock-text {
            font-weight: 700;
        }

        .stock-text.safe {
            color: #2f855a;
        }

        .stock-text.low {
            color: #c28a22;
        }

        .stock-text.empty {
            color: #c05656;
        }

        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 32px;
        }

        .flash-alert {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 1px solid;
            font-size: 14px;
        }

        .flash-alert.success {
            background: #edf7f0;
            color: #276749;
            border-color: #b7dfc4;
        }

        .flash-alert.error {
            background: #fff3f3;
            color: #c05656;
            border-color: #f0c2c2;
        }

        .pagination-wrap {
            padding: 16px 20px;
            border-top: 1px solid var(--cream-dark);
            overflow-x: auto;
        }

        .pagination-wrap nav {
            width: 100%;
        }

        .pagination-wrap .flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pagination-wrap .hidden.sm\:flex-1,
        .pagination-wrap .sm\:hidden {
            display: none !important;
        }

        .pagination-wrap .sm\:flex {
            display: flex !important;
        }

        .pagination-wrap .sm\:items-center {
            align-items: center !important;
        }

        .pagination-wrap .sm\:justify-between {
            justify-content: space-between !important;
        }

        .pagination-wrap .relative {
            position: relative;
        }

        .pagination-wrap .z-0 {
            z-index: 0;
        }

        .pagination-wrap .inline-flex {
            display: inline-flex;
        }

        .pagination-wrap .shadow-sm {
            box-shadow: none !important;
        }

        .pagination-wrap .rounded-md {
            border-radius: 12px;
        }

        .pagination-wrap .text-sm {
            font-size: 13px;
        }

        .pagination-wrap .text-gray-700,
        .pagination-wrap .text-gray-500 {
            color: var(--text-muted) !important;
        }

        .pagination-wrap .font-medium {
            font-weight: 500;
        }

        .pagination-wrap svg {
            width: 16px;
            height: 16px;
        }

        .pagination-wrap a,
        .pagination-wrap span[aria-current="page"] span,
        .pagination-wrap span[aria-disabled="true"] span {
            min-width: 40px;
            height: 40px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--cream-dark);
            background: white;
            color: var(--brown-mid);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .pagination-wrap .relative.inline-flex.items-center.px-2.py-2,
        .pagination-wrap .relative.inline-flex.items-center.px-4.py-2 {
            border-radius: 10px;
        }

        .pagination-wrap a:hover {
            background: #fdf9f4;
            border-color: var(--gold);
            color: var(--brown-dark);
        }

        .pagination-wrap span[aria-current="page"] span {
            background: var(--brown-mid);
            color: var(--gold-light);
            border-color: var(--gold);
            font-weight: 600;
        }

        .pagination-wrap span[aria-disabled="true"] span {
            color: #b8aa97;
            background: #f7f1e7;
            cursor: not-allowed;
        }

        .modal-panel {
            position: fixed;
            inset: 0;
            background: rgba(30, 17, 8, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999;
        }

        .modal-panel.show {
            display: flex;
        }

        .modal-card {
            width: 100%;
            max-width: 620px;
            background: white;
            border-radius: 18px;
            border: 1px solid var(--cream-dark);
            box-shadow: 0 20px 42px rgba(0, 0, 0, 0.14);
            overflow: hidden;
        }

        .modal-card.modal-sm {
            max-width: 460px;
        }

        .modal-card-header,
        .modal-card-footer {
            padding: 18px 22px;
            background: #fffdf9;
        }

        .modal-card-header {
            border-bottom: 1px solid var(--cream-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .modal-card-footer {
            border-top: 1px solid var(--cream-dark);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-card-header h3 {
            margin: 0;
            color: var(--brown-mid);
            font-size: 21px;
            font-weight: 400;
            font-family: 'Playfair Display', serif;
        }

        .modal-card-body {
            padding: 22px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .input-group-modern {
            display: flex;
            align-items: stretch;
            width: 100%;
        }

        .input-group-modern span {
            padding: 11px 14px;
            border: 1px solid var(--cream-dark);
            border-right: 0;
            border-radius: 12px 0 0 12px;
            background: #f8f2e8;
            color: var(--brown-mid);
            font-size: 14px;
        }

        .input-group-modern input {
            border-radius: 0 12px 12px 0;
        }

        .modal-close {
            border: 0;
            background: transparent;
            color: var(--text-muted);
            font-size: 22px;
            cursor: pointer;
            line-height: 1;
        }

        .product-note {
            color: var(--text-muted);
            margin: 0 0 14px;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .pagination-wrap .flex {
                justify-content: center !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-toolbar">
        <div>
            <h2 style="font-family:'Playfair Display', serif;font-size:24px;font-weight:400;color:var(--brown-mid);margin-bottom:6px;">
                Kelola Stok & Produk
            </h2>
            <p>Atur inventori, pantau stok, dan kelola data produk dalam satu halaman.</p>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-primary btn-sm" onclick="bukaModal('modalTambahProduk')">+ Tambah Produk</button>
        </div>
    </div>

    @if(session('success'))
        <div class="flash-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash-alert error">{{ session('error') }}</div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-value">{{ $totalProduk }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value">{{ $stokAman }}</div>
            <div class="stat-label">Stok Aman</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚠️</div>
            <div class="stat-value">{{ $stokRendah }}</div>
            <div class="stat-label">Stok Rendah</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⛔</div>
            <div class="stat-value">{{ $stokHabis }}</div>
            <div class="stat-label">Stok Habis</div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-card-body">
            <form method="GET" class="filter-grid">
                <div>
                    <label class="field-label">Cari Produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control-modern" placeholder="Cari nama produk...">
                </div>
                <div>
                    <label class="field-label">Kategori</label>
                    <select name="kategori" class="form-select-modern" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Filter Stok</label>
                    <select name="stok_filter" class="form-select-modern" onchange="this.form.submit()">
                        <option value="">Semua Stok</option>
                        <option value="aman" {{ request('stok_filter') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="rendah" {{ request('stok_filter') == 'rendah' ? 'selected' : '' }}>Stok Rendah</option>
                        <option value="habis" {{ request('stok_filter') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top:18px;">
        <div class="card-header">
            <h2>Daftar Produk</h2>
        </div>
        <div style="padding:0;overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th width="210">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $p)
                        <tr>
                            <td>{{ $products->firstItem() + $i }}</td>
                            <td>
                                <div class="product-cell">
                                    @if($p->image_url)
                                        <img src="{{ url('/foto-produk') . '?f=' . $p->image_url }}" alt="{{ $p->name }}" class="product-thumb">
                                    @else
                                        <div class="product-fallback">📦</div>
                                    @endif
                                    <span style="font-weight:600;">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-gray">{{ $p->category ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="price-text">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="stock-text {{ ($p->stock ?? 0) <= 0 ? 'empty' : (($p->stock ?? 0) <= 10 ? 'low' : 'safe') }}">
                                    {{ $p->stock ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if(($p->stock ?? 0) <= 0)
                                    <span class="badge badge-red">Habis</span>
                                @elseif(($p->stock ?? 0) <= 10)
                                    <span class="badge badge-gold">Rendah</span>
                                @else
                                    <span class="badge badge-green">Aman</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    <button type="button" class="btn btn-outline btn-sm" onclick="modalUpdateStok({{ $p->id }}, @js($p->name), {{ $p->stock ?? 0 }})">Stok</button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="modalEditProduk({{ $p->id }}, @js($p->name), {{ $p->price }}, @js($p->category))">Edit</button>
                                    <form method="POST" action="{{ route('operator.produk.hapus', $p->id) }}" style="display:inline" onsubmit="return confirm('Hapus produk {{ addslashes($p->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $products->links() }}
        </div>
    </div>

    <div class="modal-panel" id="modalTambahProduk">
        <div class="modal-card">
            <form method="POST" action="{{ route('operator.produk.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-card-header">
                    <h3>Tambah Produk</h3>
                    <button type="button" class="modal-close" onclick="tutupModal('modalTambahProduk')">&times;</button>
                </div>
                <div class="modal-card-body">
                    <div class="form-grid">
                        <div>
                            <label class="field-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control-modern" required>
                        </div>
                        <div>
                            <label class="field-label">Harga</label>
                            <div class="input-group-modern">
                                <span>Rp</span>
                                <input type="number" name="price" class="form-control-modern" required min="0">
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Kategori</label>
                            <select name="category" class="form-select-modern" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Stok Awal</label>
                            <input type="number" name="stock" class="form-control-modern" value="0" min="0">
                        </div>
                        <div>
                            <label class="field-label">Foto Produk</label>
                            <input type="file" name="image" class="form-file-modern" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-card-footer">
                    <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modalTambahProduk')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-panel" id="modalStok">
        <div class="modal-card modal-sm">
            <form method="POST" action="{{ route('operator.stok.update') }}">
                @csrf
                <input type="hidden" name="product_id" id="stokProductId">
                <div class="modal-card-header">
                    <h3>Update Stok</h3>
                    <button type="button" class="modal-close" onclick="tutupModal('modalStok')">&times;</button>
                </div>
                <div class="modal-card-body">
                    <p class="product-note">Produk: <strong id="stokNamaProduk"></strong></p>
                    <div>
                        <label class="field-label">Stok Baru</label>
                        <input type="number" name="stock" id="stokJumlah" class="form-control-modern" min="0" required>
                    </div>
                </div>
                <div class="modal-card-footer">
                    <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modalStok')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-panel" id="modalEditProduk">
        <div class="modal-card">
            <form method="POST" action="{{ route('operator.produk.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editProdukId">
                <div class="modal-card-header">
                    <h3>Edit Produk</h3>
                    <button type="button" class="modal-close" onclick="tutupModal('modalEditProduk')">&times;</button>
                </div>
                <div class="modal-card-body">
                    <div class="form-grid">
                        <div>
                            <label class="field-label">Nama Produk</label>
                            <input type="text" name="name" id="editNama" class="form-control-modern" required>
                        </div>
                        <div>
                            <label class="field-label">Harga</label>
                            <div class="input-group-modern">
                                <span>Rp</span>
                                <input type="number" name="price" id="editHarga" class="form-control-modern" required min="0">
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Kategori</label>
                            <input type="text" name="category" id="editKategori" class="form-control-modern">
                        </div>
                        <div>
                            <label class="field-label">Ganti Foto (opsional)</label>
                            <input type="file" name="image" class="form-file-modern" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-card-footer">
                    <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modalEditProduk')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function bukaModal(id) {
            document.getElementById(id).classList.add('show');
        }

        function tutupModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        function modalUpdateStok(id, nama, stokSaat) {
            document.getElementById('stokProductId').value = id;
            document.getElementById('stokNamaProduk').textContent = nama;
            document.getElementById('stokJumlah').value = stokSaat;
            bukaModal('modalStok');
        }

        function modalEditProduk(id, nama, harga, kategori) {
            document.getElementById('editProdukId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editHarga').value = harga;
            document.getElementById('editKategori').value = kategori ?? '';
            bukaModal('modalEditProduk');
        }

        document.addEventListener('click', function (event) {
            const overlay = event.target.closest('.modal-panel');
            if (overlay && event.target === overlay) {
                overlay.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-panel.show').forEach(modal => modal.classList.remove('show'));
            }
        });
    </script>
@endpush
{{-- resources/views/kasir/transaksi.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-cash-register me-2 text-success"></i>Dashboard Kasir
        </h4>
        <span class="text-muted small">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistik Hari Ini --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="text-success fs-2 fw-bold">{{ $totalTransaksiHariIni }}</div>
                <div class="text-muted small">Transaksi Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="text-primary fs-5 fw-bold">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</div>
                <div class="text-muted small">Omzet Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="text-warning fs-2 fw-bold">{{ $totalPesananPending }}</div>
                <div class="text-muted small">Pesanan Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="text-info fs-2 fw-bold">{{ $totalPesananSelesai }}</div>
                <div class="text-muted small">Pesanan Selesai</div>
            </div>
        </div>
    </div>

    {{-- Tab Menu --}}
    <ul class="nav nav-tabs mb-4" id="kasirTab">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tabPesanan">
                <i class="bi bi-bag-check me-1"></i> Kelola Pesanan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabRiwayat">
                <i class="bi bi-clock-history me-1"></i> Riwayat Transaksi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabLaporan">
                <i class="bi bi-graph-up-arrow me-1"></i> Laporan Penjualan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabProduk">
                <i class="bi bi-trophy me-1"></i> Produk Terlaris
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabPelangganAktif">
                <i class="bi bi-people me-1"></i> Pelanggan Aktif
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- TAB 1: Kelola Pesanan --}}
        <div class="tab-pane fade show active" id="tabPesanan">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Daftar Pesanan</span>
                    <select class="form-select form-select-sm w-auto" id="filterStatusPesanan" onchange="filterPesanan()">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>No. Order</th>
                                    <th>Pelanggan</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesanan as $i => $o)
                                <tr data-status="{{ $o->status }}">
                                    <td>{{ $pesanan->firstItem() + $i }}</td>
                                    <td><code>{{ $o->number }}</code></td>
                                    <td>
                                        <div>{{ $o->customer_name ?? 'Guest' }}</div>
                                        <small class="text-muted">{{ $o->customer_phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $o->order_type ?? '-' }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badge = match($o->status) {
                                                'pending'    => 'bg-warning text-dark',
                                                'processing' => 'bg-info text-dark',
                                                'completed'  => 'bg-success',
                                                'cancelled'  => 'bg-danger',
                                                default      => 'bg-secondary',
                                            };
                                            $label = match($o->status) {
                                                'pending'    => 'Pending',
                                                'processing' => 'Diproses',
                                                'completed'  => 'Selesai',
                                                'cancelled'  => 'Dibatalkan',
                                                default      => ucfirst($o->status),
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($o->created_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-secondary btn-detail"
                                                    data-number="{{ $o->number }}"
                                                    data-nama="{{ $o->customer_name }}"
                                                    data-telp="{{ $o->customer_phone }}"
                                                    data-email="{{ $o->customer_email }}"
                                                    data-tipe="{{ $o->order_type }}"
                                                    data-total="{{ number_format($o->total_price, 0, ',', '.') }}"
                                                    data-status="{{ $o->status }}"
                                                    data-alamat="{{ $o->address }}"
                                                    data-catatan="{{ $o->notes }}"
                                                    title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($o->status == 'pending')
                                            <form method="POST" action="{{ route('kasir.pesanan.update', $o->id) }}" style="display:inline">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Proses">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if($o->status == 'processing')
                                            <form method="POST" action="{{ route('kasir.pesanan.update', $o->id) }}" style="display:inline">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Selesai">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                        Belum ada pesanan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $pesanan->links() }}</div>
                </div>
            </div>
        </div>

        {{-- TAB 2: Riwayat Transaksi --}}
        <div class="tab-pane fade" id="tabRiwayat">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>No. Order</th>
                                    <th>Pelanggan</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $i => $r)
                                <tr>
                                    <td>{{ $riwayat->firstItem() + $i }}</td>
                                    <td><code>{{ $r->number }}</code></td>
                                    <td>{{ $r->customer_name ?? 'Guest' }}</td>
                                    <td>{{ $r->order_type ?? '-' }}</td>
                                    <td>Rp {{ number_format($r->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $r->status == 'completed' ? 'bg-success' : ($r->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($r->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                        Belum ada riwayat transaksi.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $riwayat->links() }}</div>
                </div>
            </div>
        </div>

        {{-- TAB 3: Laporan Penjualan --}}
        <div class="tab-pane fade" id="tabLaporan">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Grafik Penjualan</span>
                    <div class="btn-group btn-group-sm" role="group" id="btnGrupPenjualan">
                        <button type="button" class="btn btn-success active" onclick="gantiPeriodePenjualan('hari', this)">Hari Ini</button>
                        <button type="button" class="btn btn-outline-success" onclick="gantiPeriodePenjualan('minggu', this)">Minggu Ini</button>
                        <button type="button" class="btn btn-outline-success" onclick="gantiPeriodePenjualan('bulan', this)">Bulan Ini</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="grafikPenjualan" height="90"></canvas>
                </div>
            </div>

            {{-- Statistik ringkasan --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 text-center">
                        <div class="text-success fs-4 fw-bold">Rp {{ number_format($laporanOmzet, 0, ',', '.') }}</div>
                        <div class="text-muted small">Total Omzet</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 text-center">
                        <div class="text-primary fs-4 fw-bold">{{ $laporanJumlahOrder }}</div>
                        <div class="text-muted small">Total Order</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 text-center">
                        <div class="text-info fs-4 fw-bold">Rp {{ number_format($laporanRataRata, 0, ',', '.') }}</div>
                        <div class="text-muted small">Rata-rata per Order</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: Produk Terlaris --}}
        <div class="tab-pane fade" id="tabProduk">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>Grafik Produk Terlaris</span>
                        </div>
                        <div class="card-body">
                            <canvas id="grafikProduk" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-semibold"><i class="bi bi-list-ol me-2 text-warning"></i>Ranking Produk</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">#</th>
                                            <th>Produk</th>
                                            <th>Terjual</th>
                                            <th>Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topProduk as $i => $tp)
                                        <tr>
                                            <td>
                                                @if($i == 0) <span class="badge bg-warning text-dark">🥇</span>
                                                @elseif($i == 1) <span class="badge bg-secondary">🥈</span>
                                                @elseif($i == 2) <span class="badge" style="background:#cd7f32">🥉</span>
                                                @else <span class="text-muted">{{ $i + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">{{ $tp->name }}</td>
                                            <td><span class="badge bg-success">{{ $tp->total_terjual }} pcs</span></td>
                                            <td>Rp {{ number_format($tp->total_pendapatan, 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                                Belum ada data produk.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 5: Pelanggan Aktif --}}
        <div class="tab-pane fade" id="tabPelangganAktif">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-semibold"><i class="bi bi-people me-2 text-primary"></i>Grafik Pelanggan Aktif</span>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="grafikPelanggan" height="280"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-semibold"><i class="bi bi-list-stars me-2 text-primary"></i>Ranking Pelanggan Paling Sering Beli</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">#</th>
                                            <th>Pelanggan</th>
                                            <th>Total Pesanan</th>
                                            <th>Total Belanja</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topPelanggan ?? [] as $i => $tp)
                                        <tr>
                                            <td>
                                                @if($i == 0) <span class="badge bg-warning text-dark">🥇</span>
                                                @elseif($i == 1) <span class="badge bg-secondary">🥈</span>
                                                @elseif($i == 2) <span class="badge" style="background:#cd7f32">🥉</span>
                                                @else <span class="text-muted">{{ $i + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $tp->customer_name ?? 'Guest' }}</div>
                                                <small class="text-muted">{{ $tp->customer_email ?? '' }}</small>
                                            </td>
                                            <td><span class="badge bg-primary">{{ $tp->total_order }} pesanan</span></td>
                                            <td>Rp {{ number_format($tp->total_belanja ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary btn-detail-pelanggan"
                                                        data-nama="{{ $tp->customer_name }}"
                                                        title="Lihat Detail Pesanan">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                                Belum ada data pelanggan.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Detail Pesanan Per Pelanggan --}}
<div class="modal fade" id="modalDetailPelanggan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Riwayat Pesanan — <span id="dpNama"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dpBody">
                <div class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Pesanan --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan <span id="detailNomor"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr><th width="140">Pelanggan</th><td id="detailNama"></td></tr>
                    <tr><th>Telepon</th><td id="detailTelp"></td></tr>
                    <tr><th>Email</th><td id="detailEmail"></td></tr>
                    <tr><th>Tipe Order</th><td id="detailTipe"></td></tr>
                    <tr><th>Total</th><td id="detailTotal"></td></tr>
                    <tr><th>Status</th><td id="detailStatus"></td></tr>
                    <tr><th>Alamat</th><td id="detailAlamat"></td></tr>
                    <tr><th>Catatan</th><td id="detailCatatan"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@php
    // Default fallback values disiapkan di PHP dulu
    $defaultJam    = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
    $defaultZero24 = array_fill(0, 24, 0);
    $defaultHari   = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    $defaultZero7  = [0,0,0,0,0,0,0];
    $defaultTgl    = array_map(fn($d) => (string)$d, range(1, 31));
    $defaultZero31 = array_fill(0, 31, 0);

    $pHariLabels   = $grafikPenjualan['hari']['labels']   ?? $defaultJam;
    $pHariOmzet    = $grafikPenjualan['hari']['omzet']    ?? $defaultZero24;
    $pHariOrder    = $grafikPenjualan['hari']['order']    ?? $defaultZero24;

    $pMingguLabels = $grafikPenjualan['minggu']['labels'] ?? $defaultHari;
    $pMingguOmzet  = $grafikPenjualan['minggu']['omzet']  ?? $defaultZero7;
    $pMingguOrder  = $grafikPenjualan['minggu']['order']  ?? $defaultZero7;

    $pBulanLabels  = $grafikPenjualan['bulan']['labels']  ?? $defaultTgl;
    $pBulanOmzet   = $grafikPenjualan['bulan']['omzet']   ?? $defaultZero31;
    $pBulanOrder   = $grafikPenjualan['bulan']['order']   ?? $defaultZero31;

    $produkLabels  = $grafikProduk['labels']    ?? [];
    $produkValues  = $grafikProduk['values']    ?? [];

    $pelangganLabels = $grafikPelanggan['labels'] ?? [];
    $pelangganValues = $grafikPelanggan['values'] ?? [];
@endphp

<script>
// =====================================================
// DATA DARI BLADE (di-pass dari controller)
// =====================================================

const dataPenjualan = {
    hari: {
        labels: @json($pHariLabels),
        omzet:  @json($pHariOmzet),
        order:  @json($pHariOrder),
    },
    minggu: {
        labels: @json($pMingguLabels),
        omzet:  @json($pMingguOmzet),
        order:  @json($pMingguOrder),
    },
    bulan: {
        labels: @json($pBulanLabels),
        omzet:  @json($pBulanOmzet),
        order:  @json($pBulanOrder),
    },
};

const dataProduk = {
    labels: @json($produkLabels),
    values: @json($produkValues),
};

const dataPelanggan = {
    labels: @json($pelangganLabels),
    values: @json($pelangganValues),
};

// =====================================================
// GRAFIK — inisialisasi lazy saat tab dibuka
// (Chart.js tidak bisa render canvas yg hidden)
// =====================================================
let grafikPenjualan = null;
let grafikProduk    = null;
let grafikPelanggan = null;

function buatGrafikPenjualan() {
    if (grafikPenjualan) return; // sudah dibuat, skip
    const ctx = document.getElementById('grafikPenjualan').getContext('2d');
    grafikPenjualan = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dataPenjualan.hari.labels,
            datasets: [
                {
                    label: 'Omzet (Rp)',
                    data: dataPenjualan.hari.omzet,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#198754',
                    pointRadius: 4,
                    yAxisID: 'yOmzet',
                },
                {
                    label: 'Jumlah Order',
                    data: dataPenjualan.hari.order,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4,
                    yAxisID: 'yOrder',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.datasetIndex === 0) {
                                return ' Omzet: Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                            return ' Order: ' + ctx.parsed.y + ' pesanan';
                        }
                    }
                }
            },
            scales: {
                yOmzet: {
                    type: 'linear',
                    position: 'left',
                    ticks: {
                        callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v)
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                yOrder: {
                    type: 'linear',
                    position: 'right',
                    ticks: { stepSize: 1 },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

function buatGrafikProduk() {
    if (grafikProduk) return;
    const ctx = document.getElementById('grafikProduk').getContext('2d');
    grafikProduk = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataProduk.labels.length ? dataProduk.labels : ['Belum ada data'],
            datasets: [{
                label: 'Terjual (pcs)',
                data: dataProduk.values.length ? dataProduk.values : [0],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.85)',
                    'rgba(13, 110, 253, 0.75)',
                    'rgba(25, 135, 84, 0.75)',
                    'rgba(220, 53, 69, 0.75)',
                    'rgba(13, 202, 240, 0.75)',
                    'rgba(111, 66, 193, 0.75)',
                    'rgba(253, 126, 20, 0.75)',
                ],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.x + ' pcs terjual' } }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12 },
                        callback: function(val) {
                            const label = this.getLabelForValue(val);
                            return label.length > 20 ? label.substring(0, 18) + '…' : label;
                        }
                    }
                }
            }
        }
    });
}

function buatGrafikPelanggan() {
    if (grafikPelanggan) return;
    const ctx = document.getElementById('grafikPelanggan').getContext('2d');
    grafikPelanggan = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: dataPelanggan.labels.length ? dataPelanggan.labels : ['Belum ada data'],
            datasets: [{
                data: dataPelanggan.values.length ? dataPelanggan.values : [1],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.85)',
                    'rgba(25, 135, 84, 0.80)',
                    'rgba(255, 193, 7, 0.85)',
                    'rgba(220, 53, 69, 0.80)',
                    'rgba(13, 202, 240, 0.80)',
                    'rgba(111, 66, 193, 0.80)',
                    'rgba(253, 126, 20, 0.80)',
                ],
                hoverOffset: 8,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 }, boxWidth: 14 } },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' pesanan' } }
            }
        }
    });
}

// Render grafik saat tab pertama kali dibuka
document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        const target = e.target.getAttribute('href');
        if (target === '#tabLaporan')        buatGrafikPenjualan();
        if (target === '#tabProduk')         buatGrafikProduk();
        if (target === '#tabPelangganAktif') buatGrafikPelanggan();
    });
});

function gantiPeriodePenjualan(periode, btn) {
    document.querySelectorAll('#btnGrupPenjualan button').forEach(b => {
        b.classList.remove('btn-success');
        b.classList.add('btn-outline-success');
    });
    btn.classList.remove('btn-outline-success');
    btn.classList.add('btn-success');

    if (!grafikPenjualan) return;
    const d = dataPenjualan[periode];
    grafikPenjualan.data.labels = d.labels;
    grafikPenjualan.data.datasets[0].data = d.omzet;
    grafikPenjualan.data.datasets[1].data = d.order;
    grafikPenjualan.update();
}

// =====================================================
// DETAIL PESANAN PER PELANGGAN
// =====================================================
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-detail-pelanggan');
    if (!btn) return;

    const nama = btn.dataset.nama;
    document.getElementById('dpNama').textContent = nama;
    document.getElementById('dpBody').innerHTML = `
        <div class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
        </div>`;
    new bootstrap.Modal(document.getElementById('modalDetailPelanggan')).show();

    fetch(`/kasir/detail-pelanggan?customer_name=${encodeURIComponent(nama)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(orders => {
        if (!orders || orders.length === 0) {
            document.getElementById('dpBody').innerHTML = '<p class="text-muted text-center py-3">Belum ada data pesanan.</p>';
            return;
        }

        let html = '';
        orders.forEach((order, idx) => {
            const statusBadge = {
                'success': '<span class="badge bg-success">Selesai</span>',
                'completed': '<span class="badge bg-success">Selesai</span>',
                'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                'processing': '<span class="badge bg-info text-dark">Diproses</span>',
                'cancelled': '<span class="badge bg-danger">Dibatalkan</span>',
            }[order.status] || `<span class="badge bg-secondary">${order.status}</span>`;

            // Item rows
            let itemRows = '';
            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {
                    itemRows += `
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    ${item.product_image
                                        ? `<img src="/images/${item.product_image}" class="rounded" width="36" height="36" style="object-fit:cover">`
                                        : `<div class="rounded bg-warning-subtle d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:16px">☕</div>`
                                    }
                                    <span class="fw-semibold">${item.product_name}</span>
                                </div>
                            </td>
                            <td>Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                            <td>${item.quantity}x</td>
                            <td class="fw-bold">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                });
            } else {
                itemRows = `<tr><td colspan="4" class="text-muted text-center small ps-3">Data item tidak tersedia (pesanan lama)</td></tr>`;
            }

            // Kurir row
            const kurirRow = order.order_type === 'take-away' && order.ongkir > 0
                ? `<tr class="table-light">
                        <td colspan="3" class="ps-3 text-muted">🚚 Kurir: <strong>${order.courier_name} - ${order.courier_service}</strong></td>
                        <td class="fw-bold">Rp ${parseInt(order.ongkir).toLocaleString('id-ID')}</td>
                    </tr>`
                : '';

            html += `
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <div>
                            <code class="text-primary">${order.number}</code>
                            <span class="ms-2 text-muted small">${order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-'}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border">${order.order_type ?? '-'}</span>
                            ${statusBadge}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemRows}
                                ${kurirRow}
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="ps-3 fw-bold text-end pe-3">Total Pesanan</td>
                                    <td class="fw-bold text-success">Rp ${parseInt(order.total_price).toLocaleString('id-ID')}</td>
                                </tr>
                            </tfoot>
                        </table>
                        ${order.address && order.address !== '-'
                            ? `<div class="px-3 py-2 border-top small text-muted"><i class="bi bi-geo-alt me-1"></i>${order.address}</div>`
                            : ''}
                    </div>
                </div>`;
        });

        document.getElementById('dpBody').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('dpBody').innerHTML = '<p class="text-danger text-center py-3">Gagal memuat data. Coba lagi.</p>';
    });
});
function filterPesanan() {
    const status = document.getElementById('filterStatusPesanan').value;
    document.querySelectorAll('tbody tr[data-status]').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const d = btn.dataset;
    document.getElementById('detailNomor').textContent   = '#' + (d.number || '-');
    document.getElementById('detailNama').textContent    = d.nama || '-';
    document.getElementById('detailTelp').textContent    = d.telp || '-';
    document.getElementById('detailEmail').textContent   = d.email || '-';
    document.getElementById('detailTipe').textContent    = d.tipe || '-';
    document.getElementById('detailTotal').textContent   = 'Rp ' + (d.total || '0');
    document.getElementById('detailStatus').textContent  = d.status || '-';
    document.getElementById('detailAlamat').textContent  = d.alamat || '-';
    document.getElementById('detailCatatan').textContent = d.catatan || '-';
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
});
</script>

@endsection
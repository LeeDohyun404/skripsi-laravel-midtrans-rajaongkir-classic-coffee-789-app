@extends('layouts.karyawan')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Transaksi Kasir')

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
        }

        .date-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        .tab-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .tab-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--cream-dark);
            background: white;
            color: var(--brown-mid);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .tab-button:hover,
        .tab-button.active {
            background: var(--brown-mid);
            color: var(--gold-light);
            border-color: var(--gold);
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .toolbar-inline {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-inline select,
        .toolbar-inline input {
            padding: 9px 12px;
            border: 1.5px solid var(--cream-dark);
            border-radius: 8px;
            font-size: 13px;
            color: var(--text);
            background: white;
            font-family: 'DM Sans', sans-serif;
        }

        .toolbar-inline select:focus,
        .toolbar-inline input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.12);
        }

        .mono-code {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            color: var(--brown-mid);
            background: #fdf9f4;
            border: 1px solid var(--cream-dark);
            border-radius: 8px;
            padding: 4px 8px;
            display: inline-block;
        }

        .amount-text {
            font-family: 'Playfair Display', serif;
            color: var(--brown-mid);
            white-space: nowrap;
        }

        .muted-text {
            color: var(--text-muted);
            font-size: 12px;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-icon {
            min-width: 36px;
            min-height: 36px;
            justify-content: center;
            padding: 8px 10px;
        }

        .ranking-badge {
            font-size: 18px;
        }

        .badge-purple {
            background: #f3e8ff;
            color: #7c3aed;
            border: 1px solid #ddd6fe;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 32px;
        }

        .chart-box {
            padding: 20px 24px 24px;
        }

        .split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .detail-grid {
            display: grid;
            gap: 12px;
        }

        .detail-item {
            padding: 12px 14px;
            border: 1px solid var(--cream-dark);
            border-radius: 10px;
            background: #fdf9f4;
        }

        .detail-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .detail-value {
            color: var(--text);
            font-size: 14px;
            word-break: break-word;
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
            max-width: 720px;
            max-height: calc(100vh - 40px);
            overflow: auto;
            background: white;
            border-radius: 16px;
            border: 1px solid var(--cream-dark);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
            overflow-x: hidden;
        }

        .modal-card.modal-sm {
            max-width: 520px;
        }

        .modal-card-header,
        .modal-card-footer {
            padding: 18px 22px;
            border-bottom: 1px solid var(--cream-dark);
        }

        .modal-card-footer {
            border-bottom: none;
            border-top: 1px solid var(--cream-dark);
            display: flex;
            justify-content: flex-end;
        }

        .modal-card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 400;
            color: var(--brown-mid);
        }

        .modal-card-body {
            padding: 22px;
        }

        .history-order-card {
            border: 1px solid var(--cream-dark);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
            background: white;
        }

        .history-order-head {
            background: #fdf9f4;
            padding: 14px 18px;
            border-bottom: 1px solid var(--cream-dark);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .history-order-body {
            padding: 0;
        }

        .history-order-foot {
            padding: 12px 18px;
            border-top: 1px solid var(--cream-dark);
            background: #fdf9f4;
            color: var(--text-muted);
            font-size: 13px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table th,
        .mini-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--cream-dark);
            text-align: left;
            font-size: 13px;
        }

        .mini-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            background: var(--cream-dark);
        }

        .mini-table tfoot td {
            background: #fdf9f4;
            font-weight: 600;
        }

        .product-inline {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-thumb {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--cream-dark);
            flex-shrink: 0;
        }

        .product-fallback {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--cream-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fdf1d6;
            flex-shrink: 0;
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

        .pagination-wrap .hidden.sm\\:flex-1,
        .pagination-wrap .sm\\:hidden {
            display: none !important;
        }

        .pagination-wrap .sm\\:flex {
            display: flex !important;
        }

        .pagination-wrap .sm\\:items-center {
            align-items: center !important;
        }

        .pagination-wrap .sm\\:justify-between {
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

        @media (max-width: 900px) {
            .pagination-wrap .flex {
                justify-content: center !important;
            }
        }

        @media (max-width: 900px) {
            .split-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-toolbar">
        <div>
            <h2 style="font-family:'Playfair Display', serif;font-size:24px;font-weight:400;color:var(--brown-mid);margin-bottom:6px;">
                Transaksi Kasir
            </h2>
            <p>Kelola pesanan, riwayat transaksi, dan laporan penjualan dari satu halaman.</p>
        </div>
        <span class="date-label">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🧾</div>
            <div class="stat-value">{{ $totalTransaksiHariIni }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ $totalPesananPending }}</div>
            <div class="stat-label">Pesanan Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value">{{ $totalPesananSelesai }}</div>
            <div class="stat-label">Pesanan Selesai</div>
        </div>
    </div>

    <div class="tab-bar" id="kasirTabs">
        <button type="button" class="tab-button active" data-tab="tabPesanan">🛍 Kelola Pesanan</button>
        <button type="button" class="tab-button" data-tab="tabRiwayat">🕘 Riwayat Transaksi</button>
        <button type="button" class="tab-button" data-tab="tabLaporan">📈 Laporan Penjualan</button>
        <button type="button" class="tab-button" data-tab="tabProduk">🏆 Produk Terlaris</button>
        <button type="button" class="tab-button" data-tab="tabPelangganAktif">👥 Pelanggan Aktif</button>
    </div>

    <div class="tab-pane active" id="tabPesanan">
        <div class="card">
            <div class="card-header">
                <h2>Daftar Pesanan</h2>
                <div class="toolbar-inline">
                    <select id="filterStatusPesanan" onchange="filterPesanan()">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
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
                                    <td><span class="mono-code">{{ $o->number }}</span></td>
                                    <td>
                                        <div>{{ $o->customer_name ?? 'Guest' }}</div>
                                        <div class="muted-text">{{ $o->customer_phone ?? '' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-gray">{{ $o->order_type ?? '-' }}</span>
                                    </td>
                                    <td><span class="amount-text">Rp {{ number_format($o->total_price, 0, ',', '.') }}</span></td>
                                    <td>
                                        @php
                                            $badge = match($o->status) {
                                                'pending'    => 'badge-gold',
                                                'processing' => 'badge-blue',
                                                'completed'  => 'badge-green',
                                                'success'    => 'badge-green',
                                                'cancelled'  => 'badge-red',
                                                'claimed'    => 'badge-purple',
                                                default      => 'badge-gray',
                                            };
                                            $label = match($o->status) {
                                                'pending'    => 'Pending',
                                                'processing' => 'Diproses',
                                                'completed'  => 'Selesai',
                                                'success'    => 'Selesai',
                                                'cancelled'  => 'Dibatalkan',
                                                'claimed'    => '🎉 Diklaim',
                                                default      => ucfirst($o->status),
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td class="muted-text">
                                        {{ \Carbon\Carbon::parse($o->created_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline btn-sm btn-icon btn-detail"
                                                data-id="{{ $o->id }}"
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
                                                👁
                                            </button>
                                            @if($o->status == 'pending')
                                                <form method="POST" action="{{ route('kasir.pesanan.update', $o->id) }}" style="display:inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="btn btn-outline btn-sm btn-icon" title="Proses">▶</button>
                                                </form>
                                            @endif
                                            @if($o->status == 'processing')
                                                <form method="POST" action="{{ route('kasir.pesanan.update', $o->id) }}" style="display:inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-primary btn-sm btn-icon" title="Selesai">✓</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">Belum ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrap">{{ $pesanan->links() }}</div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tabRiwayat">
        <div class="card">
            <div class="card-header">
                <h2>Riwayat Transaksi</h2>
                <form method="GET" class="toolbar-inline">
                    <input type="date" name="dari" value="{{ request('dari') }}">
                    <input type="date" name="sampai" value="{{ request('sampai') }}">
                    <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                </form>
            </div>
            <div class="card-body" style="padding:0;">
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
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
                                    <td><span class="mono-code">{{ $r->number }}</span></td>
                                    <td>{{ $r->customer_name ?? 'Guest' }}</td>
                                    <td>{{ $r->order_type ?? '-' }}</td>
                                    <td><span class="amount-text">Rp {{ number_format($r->total_price, 0, ',', '.') }}</span></td>
                                    <td>
                                        <span class="badge {{ $r->status == 'completed' ? 'badge-green' : ($r->status == 'cancelled' ? 'badge-red' : 'badge-gold') }}">
                                            {{ ucfirst($r->status) }}
                                        </span>
                                    </td>
                                    <td class="muted-text">
                                        {{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">Belum ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrap">{{ $riwayat->links() }}</div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tabLaporan">
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <h2>Grafik Penjualan</h2>
                <div class="toolbar-inline" id="btnGrupPenjualan">
                    <button type="button" class="btn btn-primary btn-sm active-period" onclick="gantiPeriodePenjualan('hari', this)">Hari Ini</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="gantiPeriodePenjualan('minggu', this)">Minggu Ini</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="gantiPeriodePenjualan('bulan', this)">Bulan Ini</button>
                </div>
            </div>
            <div class="chart-box">
                <canvas id="grafikPenjualan" height="90"></canvas>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💵</div>
                <div class="stat-value">Rp {{ number_format($laporanOmzet, 0, ',', '.') }}</div>
                <div class="stat-label">Total Omzet</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧾</div>
                <div class="stat-value">{{ $laporanJumlahOrder }}</div>
                <div class="stat-label">Total Order</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value">Rp {{ number_format($laporanRataRata, 0, ',', '.') }}</div>
                <div class="stat-label">Rata-rata per Order</div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tabProduk">
        <div class="split-grid">
            <div class="card">
                <div class="card-header">
                    <h2>Grafik Produk Terlaris</h2>
                </div>
                <div class="chart-box">
                    <canvas id="grafikProduk" height="300"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2>Ranking Produk</h2>
                </div>
                <div class="card-body" style="padding:0;">
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
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
                                            @if($i == 0)
                                                <span class="ranking-badge">🥇</span>
                                            @elseif($i == 1)
                                                <span class="ranking-badge">🥈</span>
                                            @elseif($i == 2)
                                                <span class="ranking-badge">🥉</span>
                                            @else
                                                <span class="muted-text">{{ $i + 1 }}</span>
                                            @endif
                                        </td>
                                        <td style="font-weight:600;">{{ $tp->name }}</td>
                                        <td><span class="badge badge-green">{{ $tp->total_terjual }} pcs</span></td>
                                        <td><span class="amount-text">Rp {{ number_format($tp->total_pendapatan, 0, ',', '.') }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-state">Belum ada data produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tabPelangganAktif">
        <div class="split-grid" style="grid-template-columns: 0.9fr 1.1fr;">
            <div class="card">
                <div class="card-header">
                    <h2>Grafik Pelanggan Aktif</h2>
                </div>
                <div class="chart-box" style="display:flex;align-items:center;justify-content:center;">
                    <canvas id="grafikPelanggan" height="280"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2>Ranking Pelanggan Paling Sering Beli</h2>
                </div>
                <div class="card-body" style="padding:0;">
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
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
                                            @if($i == 0)
                                                <span class="ranking-badge">🥇</span>
                                            @elseif($i == 1)
                                                <span class="ranking-badge">🥈</span>
                                            @elseif($i == 2)
                                                <span class="ranking-badge">🥉</span>
                                            @else
                                                <span class="muted-text">{{ $i + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight:600;">{{ $tp->customer_name ?? 'Guest' }}</div>
                                            <div class="muted-text">{{ $tp->customer_email ?? '' }}</div>
                                        </td>
                                        <td><span class="badge badge-blue">{{ $tp->total_order }} pesanan</span></td>
                                        <td><span class="amount-text">Rp {{ number_format($tp->total_belanja ?? 0, 0, ',', '.') }}</span></td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-outline btn-sm btn-icon btn-detail-pelanggan"
                                                data-nama="{{ $tp->customer_name }}"
                                                title="Lihat Detail Pesanan">
                                                👁
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-state">Belum ada data pelanggan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-panel" id="modalDetailPelanggan">
        <div class="modal-card">
            <div class="modal-card-header">
                <h3>Riwayat Pesanan — <span id="dpNama"></span></h3>
            </div>
            <div class="modal-card-body" id="dpBody">
                <div class="empty-state">Memuat data...</div>
            </div>
            <div class="modal-card-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modalDetailPelanggan')">Tutup</button>
            </div>
        </div>
    </div>

 <div class="modal-panel" id="modalDetail">
    <div class="modal-card" style="max-width:660px;">
        <div class="modal-card-header">
            <h3>Detail Pesanan <span id="detailNomor"></span></h3>
        </div>
        <div class="modal-card-body">
 
            {{-- Info Pelanggan --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">
                <div class="detail-item"><span class="detail-label">Pelanggan</span><div class="detail-value" id="detailNama"></div></div>
                <div class="detail-item"><span class="detail-label">Telepon</span><div class="detail-value" id="detailTelp"></div></div>
                <div class="detail-item"><span class="detail-label">Email</span><div class="detail-value" id="detailEmail"></div></div>
                <div class="detail-item"><span class="detail-label">Tipe Order</span><div class="detail-value" id="detailTipe"></div></div>
                <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value" id="detailStatus"></div></div>
                <div class="detail-item" id="detailKurirWrap" style="display:none;">
                    <span class="detail-label">Kurir</span>
                    <div class="detail-value" id="detailKurir"></div>
                </div>
                <div class="detail-item" style="grid-column:1/-1;"><span class="detail-label">Alamat</span><div class="detail-value" id="detailAlamat"></div></div>
                <div class="detail-item" style="grid-column:1/-1;"><span class="detail-label">Catatan</span><div class="detail-value" id="detailCatatan"></div></div>
            </div>
 
            {{-- Tabel Produk --}}
            <h4 style="font-family:'Playfair Display',serif;color:var(--brown-mid);margin-bottom:10px;font-size:16px;font-weight:400;">
                🛍 Produk yang Dibeli
            </h4>
            <div style="border:1px solid var(--cream-dark);border-radius:10px;overflow:hidden;">
                <table class="mini-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="text-align:center;width:60px;">Qty</th>
                            <th style="text-align:right;width:110px;">Harga</th>
                            <th style="text-align:right;width:110px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detailItemBody">
                        <tr><td colspan="4" class="empty-state">Memuat...</td></tr>
                    </tbody>
                    <tfoot>
                        <tr id="detailRowSubtotal" style="display:none;">
                            <td colspan="3" style="text-align:right;color:var(--text-muted);font-size:13px;">Subtotal</td>
                            <td style="text-align:right;" id="detailSubtotalVal"></td>
                        </tr>
                        <tr id="detailRowOngkir" style="display:none;">
                            <td colspan="3" style="text-align:right;color:var(--text-muted);font-size:13px;">🚚 Ongkos Kirim</td>
                            <td style="text-align:right;" id="detailOngkirVal"></td>
                        </tr>
                        <tr id="detailRowVoucher" style="display:none;">
                            <td colspan="3" style="text-align:right;color:#b45309;font-size:13px;">🎉 Diskon Voucher</td>
                            <td style="text-align:right;color:#b45309;font-weight:600;" id="detailVoucherVal"></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:700;">Total</td>
                            <td style="text-align:right;font-weight:700;" id="detailTotalVal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
 
        </div>
        <div class="modal-card-footer">
            <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modalDetail')">Tutup</button>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@php
    $defaultJam = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
    $defaultZero24 = array_fill(0, 24, 0);
    $defaultHari = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    $defaultZero7 = [0,0,0,0,0,0,0];
    $defaultTgl = array_map(fn($d) => (string)$d, range(1, 31));
    $defaultZero31 = array_fill(0, 31, 0);

    $pHariLabels = $grafikPenjualan['hari']['labels'] ?? $defaultJam;
    $pHariOmzet = $grafikPenjualan['hari']['omzet'] ?? $defaultZero24;
    $pHariOrder = $grafikPenjualan['hari']['order'] ?? $defaultZero24;

    $pMingguLabels = $grafikPenjualan['minggu']['labels'] ?? $defaultHari;
    $pMingguOmzet = $grafikPenjualan['minggu']['omzet'] ?? $defaultZero7;
    $pMingguOrder = $grafikPenjualan['minggu']['order'] ?? $defaultZero7;

    $pBulanLabels = $grafikPenjualan['bulan']['labels'] ?? $defaultTgl;
    $pBulanOmzet = $grafikPenjualan['bulan']['omzet'] ?? $defaultZero31;
    $pBulanOrder = $grafikPenjualan['bulan']['order'] ?? $defaultZero31;

    $produkLabels = $grafikProduk['labels'] ?? [];
    $produkValues = $grafikProduk['values'] ?? [];

    $pelangganLabels = $grafikPelanggan['labels'] ?? [];
    $pelangganValues = $grafikPelanggan['values'] ?? [];
@endphp

@push('scripts')
    <script>
        const dataPenjualan = {
            hari: {
                labels: @json($pHariLabels),
                omzet: @json($pHariOmzet),
                order: @json($pHariOrder),
            },
            minggu: {
                labels: @json($pMingguLabels),
                omzet: @json($pMingguOmzet),
                order: @json($pMingguOrder),
            },
            bulan: {
                labels: @json($pBulanLabels),
                omzet: @json($pBulanOmzet),
                order: @json($pBulanOrder),
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

        let grafikPenjualan = null;
        let grafikProduk = null;
        let grafikPelanggan = null;

        function aktifkanTab(tabId) {
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.tab === tabId);
            });

            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.toggle('active', pane.id === tabId);
            });

            if (tabId === 'tabLaporan') buatGrafikPenjualan();
            if (tabId === 'tabProduk') buatGrafikProduk();
            if (tabId === 'tabPelangganAktif') buatGrafikPelanggan();
        }

        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                aktifkanTab(this.dataset.tab);
            });
        });

        function buatGrafikPenjualan() {
            if (grafikPenjualan) return;
            const ctx = document.getElementById('grafikPenjualan').getContext('2d');
            grafikPenjualan = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dataPenjualan.hari.labels,
                    datasets: [
                        {
                            label: 'Omzet (Rp)',
                            data: dataPenjualan.hari.omzet,
                            borderColor: '#6b3f1f',
                            backgroundColor: 'rgba(200, 169, 110, 0.18)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#6b3f1f',
                            pointRadius: 4,
                            yAxisID: 'yOmzet',
                        },
                        {
                            label: 'Jumlah Order',
                            data: dataPenjualan.hari.order,
                            borderColor: '#c8a96e',
                            backgroundColor: 'rgba(200, 169, 110, 0.08)',
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#c8a96e',
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
                                callback: v => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + 'jt' : v >= 1000 ? (v / 1000).toFixed(0) + 'rb' : v)
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
                            'rgba(200, 169, 110, 0.95)',
                            'rgba(107, 63, 31, 0.78)',
                            'rgba(61, 36, 16, 0.75)',
                            'rgba(232, 213, 163, 0.95)',
                            'rgba(158, 122, 74, 0.82)',
                            'rgba(245, 232, 205, 0.95)',
                            'rgba(140, 96, 58, 0.82)',
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
                            'rgba(107, 63, 31, 0.88)',
                            'rgba(200, 169, 110, 0.92)',
                            'rgba(61, 36, 16, 0.82)',
                            'rgba(232, 213, 163, 0.96)',
                            'rgba(158, 122, 74, 0.82)',
                            'rgba(245, 232, 205, 0.98)',
                            'rgba(140, 96, 58, 0.82)',
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

        function gantiPeriodePenjualan(periode, btn) {
            document.querySelectorAll('#btnGrupPenjualan button').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline');
            });
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');

            if (!grafikPenjualan) return;
            const d = dataPenjualan[periode];
            grafikPenjualan.data.labels = d.labels;
            grafikPenjualan.data.datasets[0].data = d.omzet;
            grafikPenjualan.data.datasets[1].data = d.order;
            grafikPenjualan.update();
        }

        function bukaModal(id) {
            document.getElementById(id).classList.add('show');
        }

        function tutupModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        document.addEventListener('click', function(event) {
            const overlay = event.target.closest('.modal-panel');
            if (overlay && event.target === overlay) {
                overlay.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-panel.show').forEach(modal => modal.classList.remove('show'));
            }
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-detail-pelanggan');
            if (!btn) return;

            const nama = btn.dataset.nama;
            document.getElementById('dpNama').textContent = nama;
            document.getElementById('dpBody').innerHTML = '<div class="empty-state">Memuat data...</div>';
            bukaModal('modalDetailPelanggan');

            fetch(`/kasir/detail-pelanggan?customer_name=${encodeURIComponent(nama)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(orders => {
                if (!orders || orders.length === 0) {
                    document.getElementById('dpBody').innerHTML = '<div class="empty-state">Belum ada data pesanan.</div>';
                    return;
                }

                let html = '';
                orders.forEach((order, idx) => {
                    const statusBadge = {
                        'success': '<span class="badge badge-green">Selesai</span>',
                        'completed': '<span class="badge badge-green">Selesai</span>',
                        'pending': '<span class="badge badge-gold">Pending</span>',
                        'processing': '<span class="badge badge-blue">Diproses</span>',
                        'cancelled': '<span class="badge badge-red">Dibatalkan</span>',
                    }[order.status] || `<span class="badge badge-gray">${order.status}</span>`;

                    let itemRows = '';
                    if (order.items && order.items.length > 0) {
                        order.items.forEach(item => {
                            itemRows += `
                                <tr>
                                    <td>
                                        <div class="product-inline">
                                            ${item.product_image
                                                ? `<img src="/images/${item.product_image}" class="product-thumb" alt="${item.product_name}">`
                                                : `<div class="product-fallback">☕</div>`
                                            }
                                            <span style="font-weight:600;">${item.product_name}</span>
                                        </div>
                                    </td>
                                    <td>Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                                    <td>${item.quantity}x</td>
                                    <td style="font-weight:600;">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                                </tr>`;
                        });
                    } else {
                        itemRows = `<tr><td colspan="4" class="empty-state" style="padding:16px;">Data item tidak tersedia (pesanan lama)</td></tr>`;
                    }

                    const kurirRow = order.order_type === 'take-away' && order.ongkir > 0
                        ? `<tr>
                                <td colspan="3" style="color:var(--text-muted);">🚚 Kurir: <strong>${order.courier_name} - ${order.courier_service}</strong></td>
                                <td style="font-weight:600;">Rp ${parseInt(order.ongkir).toLocaleString('id-ID')}</td>
                            </tr>`
                        : '';

                    html += `
                        <div class="history-order-card">
                            <div class="history-order-head">
                                <div>
                                    <span class="mono-code">${order.number}</span>
                                    <span class="muted-text" style="margin-left:8px;">
                                        ${order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-'}
                                    </span>
                                </div>
                                <div class="action-group">
                                    <span class="badge badge-gray">${order.order_type ?? '-'}</span>
                                    ${statusBadge}
                                </div>
                            </div>
                            <div class="history-order-body">
                                <table class="mini-table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${itemRows}
                                        ${kurirRow}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align:right;">Total Pesanan</td>
                                            <td>Rp ${parseInt(order.total_price).toLocaleString('id-ID')}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            ${order.address && order.address !== '-'
                                ? `<div class="history-order-foot">📍 ${order.address}</div>`
                                : ''}
                        </div>`;
                });

                document.getElementById('dpBody').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('dpBody').innerHTML = '<div class="empty-state" style="color:#c0392b;">Gagal memuat data. Coba lagi.</div>';
            });
        });

        function filterPesanan() {
            const status = document.getElementById('filterStatusPesanan').value;
            document.querySelectorAll('#tabPesanan tbody tr[data-status]').forEach(row => {
                row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
            });
        }

      document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-detail');
            if (!btn) return;
 
            const d = btn.dataset;
 
            // Isi info statis dari data-* dulu (langsung tampil cepat)
            document.getElementById('detailNomor').textContent   = '#' + (d.number  || '-');
            document.getElementById('detailNama').textContent    = d.nama    || '-';
            document.getElementById('detailTelp').textContent    = d.telp    || '-';
            document.getElementById('detailEmail').textContent   = d.email   || '-';
            document.getElementById('detailTipe').textContent    = d.tipe    || '-';
            document.getElementById('detailStatus').textContent  = d.status  || '-';
            document.getElementById('detailAlamat').textContent  = d.alamat  || '-';
            document.getElementById('detailCatatan').textContent = d.catatan || '-';
 
            // Reset tabel & kurir
            document.getElementById('detailItemBody').innerHTML         = '<tr><td colspan="4" class="empty-state">Memuat...</td></tr>';
            document.getElementById('detailRowSubtotal').style.display  = 'none';
            document.getElementById('detailRowOngkir').style.display    = 'none';
            document.getElementById('detailKurirWrap').style.display    = 'none';
            document.getElementById('detailTotalVal').textContent       = 'Rp ' + (d.total || '0');
 
            bukaModal('modalDetail');
 
            // Fetch detail items via AJAX
            fetch(`/kasir/detail-pesanan/${d.id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
 
                // Kurir — tampilkan jika ada
                if (data.courier_name && data.courier_name !== '-' && data.courier_name !== null) {
                    const layanan = data.courier_service && data.courier_service !== '-'
                        ? ' — ' + data.courier_service
                        : '';
                    document.getElementById('detailKurir').textContent = data.courier_name + layanan;
                    document.getElementById('detailKurirWrap').style.display = '';
                }
 
                // Baris produk
                if (!data.items || data.items.length === 0) {
                    document.getElementById('detailItemBody').innerHTML =
                        '<tr><td colspan="4" class="empty-state">Tidak ada item untuk pesanan ini.</td></tr>';
                } else {
                    let rows = '';
                    data.items.forEach(item => {
                        const thumb = item.product_image
                            ? `<img src="/images/${item.product_image}" class="product-thumb" alt="${item.product_name}">`
                            : `<div class="product-fallback">☕</div>`;
 
                        rows += `<tr>
                            <td>
                                <div class="product-inline">
                                    ${thumb}
                                    <span style="font-weight:600;">${item.product_name}</span>
                                </div>
                            </td>
                            <td style="text-align:center;">${item.quantity}×</td>
                            <td style="text-align:right;">Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                            <td style="text-align:right;font-weight:600;">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                    });
                    document.getElementById('detailItemBody').innerHTML = rows;
                }
 
                // Subtotal
                if (data.subtotal > 0) {
                    document.getElementById('detailSubtotalVal').textContent = 'Rp ' + parseInt(data.subtotal).toLocaleString('id-ID');
                    document.getElementById('detailRowSubtotal').style.display = '';
                }
 
                // Ongkir
                if (data.ongkir > 0) {
                    document.getElementById('detailOngkirVal').textContent = 'Rp ' + parseInt(data.ongkir).toLocaleString('id-ID');
                    document.getElementById('detailRowOngkir').style.display = '';
                }

                // Diskon Voucher
                if (data.voucher_discount > 0) {
                    document.getElementById('detailVoucherVal').textContent = '- Rp ' + parseInt(data.voucher_discount).toLocaleString('id-ID');
                    document.getElementById('detailRowVoucher').style.display = '';
                }
 
                // Total final dari server
                document.getElementById('detailTotalVal').textContent = 'Rp ' + parseInt(data.total_price).toLocaleString('id-ID');
            })
            .catch(() => {
                document.getElementById('detailItemBody').innerHTML =
                    '<tr><td colspan="4" class="empty-state" style="color:#c0392b;">Gagal memuat data. Coba lagi.</td></tr>';
            });
        });
    </script>
@endpush
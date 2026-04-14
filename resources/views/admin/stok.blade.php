@extends('layouts.karyawan')

@section('title', 'Lihat Stok Produk')
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
        }

        .page-toolbar p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stock-card {
            background: white;
            border-radius: 14px;
            padding: 22px 24px;
            border: 1px solid var(--cream-dark);
            position: relative;
            overflow: hidden;
        }

        .stock-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold);
        }

        .stock-value {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--brown-mid);
            margin-bottom: 6px;
        }

        .stock-label {
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--cream-dark);
            flex-shrink: 0;
        }

        .product-name {
            font-weight: 600;
            color: var(--text);
        }

        .price-text {
            font-family: 'Playfair Display', serif;
            color: var(--brown-mid);
            white-space: nowrap;
        }

        .stock-count {
            font-weight: 700;
        }

        .stock-safe {
            color: #2e7d52;
        }

        .stock-low {
            color: #8a6520;
        }

        .stock-empty {
            color: #c0392b;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 32px;
        }

        @media (max-width: 900px) {
            .page-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-toolbar">
        <div>
            <h2 style="font-family:'Playfair Display', serif;font-size:24px;font-weight:400;color:var(--brown-mid);margin-bottom:6px;">
                Monitoring Stok Produk
            </h2>
            <p>Ringkasan persediaan seluruh produk yang tersedia di Classic Coffee 789.</p>
        </div>
        <span class="badge badge-gray">Mode: Hanya Lihat</span>
    </div>

    <div class="stock-grid">
        <div class="stock-card">
            <div class="stock-value">{{ $totalProduk }}</div>
            <div class="stock-label">Total Produk</div>
        </div>
        <div class="stock-card">
            <div class="stock-value">{{ $stokAman }}</div>
            <div class="stock-label">Stok Aman</div>
        </div>
        <div class="stock-card">
            <div class="stock-value">{{ $stokRendah }}</div>
            <div class="stock-label">Stok Rendah</div>
        </div>
        <div class="stock-card">
            <div class="stock-value">{{ $stokHabis }}</div>
            <div class="stock-label">Stok Habis</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Daftar Produk & Stok</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
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
                                    <div class="product-cell">
                                        <img
                                            src="{{ asset('images/'.$p->image_url) }}"
                                            alt="{{ $p->name }}"
                                            class="product-image"
                                            onerror="this.src='https://placehold.co/44x44?text=?'">
                                        <span class="product-name">{{ $p->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-gray">{{ $p->category ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="price-text">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="stock-count {{ ($p->stock ?? 0) <= 0 ? 'stock-empty' : ($p->stock <= 10 ? 'stock-low' : 'stock-safe') }}">
                                        {{ $p->stock ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if(($p->stock ?? 0) <= 0)
                                        <span class="badge badge-red">Habis</span>
                                    @elseif($p->stock <= 10)
                                        <span class="badge badge-gold">Rendah</span>
                                    @else
                                        <span class="badge badge-green">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    Belum ada produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

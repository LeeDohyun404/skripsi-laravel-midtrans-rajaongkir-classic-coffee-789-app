@extends('layouts.karyawan')

@section('title', 'History Pesanan')
@section('page-title', 'History Pesanan')

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

        .order-id {
            font-weight: 600;
            color: var(--brown-mid);
            display: block;
            margin-bottom: 4px;
        }

        .muted-text {
            color: var(--text-muted);
            font-size: 12px;
        }

        .customer-name {
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .customer-contact {
            color: #2e7d52;
            font-size: 12px;
        }

        .note-text {
            color: #8a6520;
            font-size: 12px;
            font-style: italic;
        }

        .amount {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: var(--brown-mid);
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
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
                Riwayat Pesanan
            </h2>
            <p>Daftar seluruh transaksi pelanggan yang sudah masuk ke sistem.</p>
        </div>
        <span class="badge badge-gold">Total: {{ count($orders) }} transaksi</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Daftar History Transaksi</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID & Waktu</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Alamat / Catatan</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td>
                                    <span class="order-id">#{{ substr($o->number, -8) }}</span>
                                    <span class="muted-text">{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y, H:i') }}</span>
                                </td>
                                <td>
                                    <span class="customer-name">{{ $o->customer_name }}</span>
                                    <div class="customer-contact">WhatsApp: {{ $o->customer_phone }}</div>
                                    <div class="muted-text">{{ $o->customer_email }}</div>
                                </td>
                                <td>
                                    @if(($o->order_type ?? '') == 'dine-in')
                                        <span class="badge badge-blue">Makan di Sini</span>
                                    @else
                                        <span class="badge badge-gold">Bawa Pulang</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="muted-text">
                                        {{ ($o->order_type ?? '') == 'dine-in' ? 'Pesanan di Tempat' : ($o->address ?? '-') }}
                                    </div>
                                    @if($o->notes)
                                        <div class="note-text">"{{ $o->notes }}"</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="amount">Rp {{ number_format($o->total_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ in_array($o->status, ['success', 'settlement', 'capture', 'completed']) ? 'badge-green' : 'badge-gold' }}">
                                        {{ ucfirst($o->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    Belum ada history transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

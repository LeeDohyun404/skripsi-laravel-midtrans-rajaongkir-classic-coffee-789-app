@extends('layouts.karyawan')

@section('title', 'Kelola Pelanggan')
@section('page-title', 'Pelanggan')

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

        .stats-grid-custom {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .customer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--cream-dark);
            flex-shrink: 0;
        }

        .avatar-placeholder {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--brown-mid);
            color: var(--gold-light);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-name {
            font-weight: 600;
            color: var(--text);
        }

        .muted-text {
            color: var(--text-muted);
            font-size: 12px;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-icon {
            min-width: 36px;
            min-height: 36px;
            justify-content: center;
            padding: 8px 10px;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 32px;
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
            max-width: 420px;
            background: white;
            border-radius: 16px;
            border: 1px solid var(--cream-dark);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
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
                Kelola Pelanggan
            </h2>
            <p>Daftar pelanggan yang telah terdaftar dan ringkasan aktivitas pendaftaran.</p>
        </div>
        <span class="badge badge-blue">Panel Pelanggan</span>
    </div>

    <div class="stats-grid stats-grid-custom">
        <div class="stat-card">
            <div class="stat-icon">🧑‍🤝‍🧑</div>
            <div class="stat-value">{{ count($customers) }}</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-value">{{ $pelangganBulanIni }}</div>
            <div class="stat-label">Bergabung Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✨</div>
            <div class="stat-value">{{ $pelangganHariIni }}</div>
            <div class="stat-label">Bergabung Hari Ini</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Daftar Pelanggan</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Login Via</th>
                            <th>Tanggal Daftar</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="customer-info">
                                        @if($c->avatar)
                                            <img src="{{ $c->avatar }}" alt="{{ $c->name }}" class="customer-avatar">
                                        @else
                                            <div class="avatar-placeholder">{{ strtoupper(substr($c->name, 0, 1)) }}</div>
                                        @endif
                                        <span class="customer-name">{{ $c->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $c->email ?? '-' }}</td>
                                <td>
                                    @if($c->google_id)
                                        <span class="badge badge-red">Google</span>
                                    @else
                                        <span class="badge badge-gray">Email</span>
                                    @endif
                                </td>
                                <td class="muted-text">
                                    {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="btn btn-outline btn-sm btn-icon"
                                            title="Detail"
                                            data-nama="{{ $c->name }}"
                                            data-email="{{ $c->email }}"
                                            data-login="{{ $c->google_id ? 'Google' : 'Email' }}"
                                            data-join="{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y H:i') }}"
                                            onclick="lihatDetail(this)">
                                            👁
                                        </button>
                                        <form method="POST" action="/admin/pelanggan/{{ $c->id }}/hapus" style="display:inline"
                                            onsubmit="return confirm('Hapus pelanggan {{ addslashes($c->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    Belum ada pelanggan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-panel" id="modalDetail">
        <div class="modal-card">
            <div class="modal-card-header">
                <h3>Detail Pelanggan</h3>
            </div>
            <div class="modal-card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nama</span>
                        <div class="detail-value" id="detailNama"></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <div class="detail-value" id="detailEmail"></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Login Via</span>
                        <div class="detail-value" id="detailLogin"></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bergabung</span>
                        <div class="detail-value" id="detailJoin"></div>
                    </div>
                </div>
            </div>
            <div class="modal-card-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="tutupDetail()">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function lihatDetail(btn) {
            document.getElementById('detailNama').textContent = btn.dataset.nama || '-';
            document.getElementById('detailEmail').textContent = btn.dataset.email || '-';
            document.getElementById('detailLogin').textContent = btn.dataset.login || '-';
            document.getElementById('detailJoin').textContent = btn.dataset.join || '-';
            document.getElementById('modalDetail').classList.add('show');
        }

        function tutupDetail() {
            document.getElementById('modalDetail').classList.remove('show');
        }

        document.addEventListener('click', function (event) {
            const modal = document.getElementById('modalDetail');
            if (event.target === modal) {
                tutupDetail();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                tutupDetail();
            }
        });
    </script>
@endpush

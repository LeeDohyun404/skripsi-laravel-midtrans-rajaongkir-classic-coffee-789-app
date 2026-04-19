<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Classic Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brown-dark:  #1e1108;
            --brown-mid:   #3d2410;
            --brown-warm:  #6b3f1f;
            --gold:        #c8a96e;
            --gold-light:  #e8d5a3;
            --cream:       #faf7f2;
            --cream-dark:  #f0ead8;
            --text:        #2c1a0e;
            --text-muted:  #9e7a4a;
            --sidebar-w:   240px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream-dark);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ============ SIDEBAR ============ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--brown-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            transition: transform 0.3s;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(200,169,110,0.15);
        }

        .sidebar-brand .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--cream);
        }

        .sidebar-brand .logo-sub {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-top: 2px;
        }

        /* Nav */
        .sidebar-nav {
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(158,122,74,0.5);
            padding: 12px 24px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 24px;
            font-size: 13.5px;
            color: rgba(250,247,242,0.6);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: rgba(200,169,110,0.08);
            color: var(--cream);
        }

        .nav-item.active {
            background: rgba(200,169,110,0.12);
            color: var(--gold);
            border-left-color: var(--gold);
        }

        .nav-item .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* User info at bottom */
        .sidebar-user {
            padding: 16px 24px;
            border-top: 1px solid rgba(200,169,110,0.15);
        }

        .user-name {
            font-size: 13px;
            color: var(--cream);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 10px;
            color: var(--gold);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .btn-logout {
            margin-top: 12px;
            display: block;
            width: 100%;
            padding: 9px;
            background: transparent;
            color: rgba(200,169,110,0.6);
            border: 1px solid rgba(200,169,110,0.2);
            border-radius: 8px;
            font-size: 12px;
            letter-spacing: 1px;
            text-align: center;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(192,57,43,0.15);
            color: #e87c71;
            border-color: rgba(192,57,43,0.3);
        }

        /* ============ MAIN CONTENT ============ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: white;
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--cream-dark);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .topbar h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 400;
            color: var(--text);
        }

        .topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar .greeting {
            font-size: 13px;
            color: var(--text-muted);
        }

        .topbar .greeting span {
            color: var(--brown-mid);
            font-weight: 500;
        }

        /* Page content */
        .page-content {
            padding: 32px;
            flex: 1;
        }

        /* Flash messages */
        .flash {
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13.5px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash.success {
            background: #edf7f1;
            border: 1px solid #a8dabb;
            color: #2e7d52;
        }

        .flash.error {
            background: #fdf0ef;
            border: 1px solid #f5c0bc;
            color: #c0392b;
        }

        .flash.warning {
            background: #fff8e8;
            border: 1px solid #f0d58a;
            color: #8a6520;
        }

        /* ============ CARD ============ */
        .card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--cream-dark);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--cream-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 400;
            color: var(--text);
        }

        .card-body {
            padding: 24px;
        }

        /* ============ STAT CARDS ============ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 22px 24px;
            border: 1px solid var(--cream-dark);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold);
        }

        .stat-card .stat-icon {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .stat-card .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--brown-mid);
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }

        /* ============ TABLE ============ */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        thead tr {
            background: var(--cream-dark);
        }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 500;
        }

        tbody tr {
            border-bottom: 1px solid var(--cream-dark);
            transition: background 0.15s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fdf9f4; }

        tbody td {
            padding: 13px 16px;
            color: var(--text);
            vertical-align: middle;
        }

        /* ============ BADGES ============ */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .badge-gold    { background: #fff3d6; color: #8a6520; }
        .badge-green   { background: #e6f4ee; color: #2e7d52; }
        .badge-red     { background: #fdecea; color: #c0392b; }
        .badge-blue    { background: #e8f0fc; color: #2557a7; }
        .badge-gray    { background: #f2f2f2; color: #666; }

        /* ============ BUTTONS ============ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: all 0.15s;
            font-weight: 500;
        }

        .btn-primary {
            background: var(--brown-mid);
            color: var(--gold);
            border-color: var(--gold);
        }

        .btn-primary:hover {
            background: var(--gold);
            color: var(--brown-mid);
        }

        .btn-outline {
            background: transparent;
            color: var(--brown-mid);
            border-color: var(--cream-dark);
        }

        .btn-outline:hover {
            border-color: var(--gold);
            color: var(--brown-warm);
        }

        .btn-danger {
            background: transparent;
            color: #c0392b;
            border-color: #f5c0bc;
        }

        .btn-danger:hover {
            background: #fdecea;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* ============ FORMS ============ */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-group label {
            font-size: 12px;
            font-weight: 500;
            color: var(--brown-warm);
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 14px;
            background: white;
            border: 1.5px solid var(--cream-dark);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200,169,110,0.12);
        }

        .form-group .field-error {
            font-size: 12px;
            color: #c0392b;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-text">☕ Classic Coffee</div>
        <div class="logo-sub">{{ ucfirst(auth('karyawan')->user()->peran ?? '') }} Panel</div>
    </div>

    <nav class="sidebar-nav">
        @yield('sidebar-nav')
    </nav>

    <div class="sidebar-user">
        <div class="user-name">{{ auth('karyawan')->user()->nama ?? '' }}</div>
        <div class="user-role">{{ ucfirst(auth('karyawan')->user()->peran ?? '') }}</div>
        <form method="POST" action="{{ route('staff.logout') }}">
            @csrf
            <button class="btn-logout" type="submit">Keluar ↩</button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main">
    <div class="topbar">
        <h1>@yield('page-title', 'Dashboard')</h1>
        <div class="topbar-right">
            {{-- Dropdown Profil --}}
            <div style="position:relative;display:inline-block;" id="profil-wrapper">
                <button onclick="toggleDropdownProfil()" style="display:flex;align-items:center;gap:8px;padding:6px 14px;border:1px solid var(--cream-dark);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;">
                    <span style="width:30px;height:30px;border-radius:50%;background:var(--gold-light);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--brown-mid);">
                        {{ strtoupper(substr(auth('karyawan')->user()->nama ?? 'U', 0, 2)) }}
                    </span>
                    <span>{{ auth('karyawan')->user()->nama ?? '' }}</span>
                    <span style="font-size:10px;opacity:0.5;">▼</span>
                </button>
                <div id="dropdown-profil" style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:#fff;border:1px solid var(--cream-dark);border-radius:10px;width:200px;box-shadow:0 4px 20px rgba(0,0,0,.10);z-index:999;overflow:hidden;">
                    <div style="padding:10px 14px 8px;border-bottom:1px solid var(--cream-dark);">
                        <div style="font-size:12px;font-weight:600;color:var(--text);">{{ auth('karyawan')->user()->nama ?? '' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ ucfirst(auth('karyawan')->user()->peran ?? '') }}</div>
                    </div>
                    <button onclick="bukaModalProfil('nama'); toggleDropdownProfil();" style="width:100%;padding:10px 14px;text-align:left;font-size:13px;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;color:var(--text);">
                        ✏️ Ganti nama
                    </button>
                    <button onclick="bukaModalProfil('password'); toggleDropdownProfil();" style="width:100%;padding:10px 14px;text-align:left;font-size:13px;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;color:var(--text);">
                        🔑 Ubah password
                    </button>
                    <div style="border-top:1px solid var(--cream-dark);">
                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;padding:10px 14px;text-align:left;font-size:13px;color:#dc2626;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;">
                                🚪 Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
        function toggleDropdownProfil() {
            const dd = document.getElementById('dropdown-profil');
            dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('profil-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('dropdown-profil').style.display = 'none';
            }
        });
        </script>
    </div>

    <div class="page-content">

        @if (session('success'))
            <div class="flash success">✅ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash error">❌ {{ session('error') }}</div>
        @endif
        @if (session('warning'))
            <div class="flash warning">⚠️ {{ session('warning') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
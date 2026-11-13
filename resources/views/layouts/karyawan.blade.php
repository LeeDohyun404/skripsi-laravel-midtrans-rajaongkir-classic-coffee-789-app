<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Classic Coffee Staff</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Modern Slate Tech Palette */
            --slate-950: #020617;
            --slate-900: #0f172a;
            --slate-850: #131d33;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            
            /* Professional Vibrant Accents */
            --primary:       #4f46e5; /* Indigo */
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-border:#c7d2fe;
            --primary-glow:  rgba(79, 70, 229, 0.16);
            
            --emerald:       #10b981;
            --emerald-hover: #059669;
            --emerald-light: #ecfdf5;
            --emerald-border:#a7f3d0;
            
            --amber:         #f59e0b;
            --amber-hover:   #d97706;
            --amber-light:   #fffbeb;
            --amber-border:  #fde68a;
            
            --rose:          #f43f5e;
            --rose-hover:    #e11d48;
            --rose-light:    #fff1f2;
            --rose-border:   #fecdd3;
            
            --sky:           #0284c7;
            --sky-hover:     #0369a1;
            --sky-light:     #f0f9ff;
            --sky-border:    #bae6fd;
            
            /* Compatibility Aliases for Existing Views */
            --brown-dark:    #0f172a;
            --brown-mid:     #1e293b;
            --brown-warm:    #334155;
            --gold:          #4f46e5;
            --gold-light:    #eef2ff;
            --cream:         #ffffff;
            --cream-dark:    #e2e8f0;
            --text:          #0f172a;
            --text-muted:    #64748b;
            
            --sidebar-w:     260px;
            --topbar-h:      70px;
            --radius-sm:     8px;
            --radius-md:     12px;
            --radius-lg:     16px;
            --radius-xl:     20px;
            
            --shadow-subtle: 0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.03);
            --shadow-card:   0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
            --shadow-hover:  0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03);
            --shadow-drawer: -4px 0 25px rgba(0, 0, 0, 0.25);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--slate-50);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--slate-900);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            border-right: 1px solid var(--slate-800);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 24px 22px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.35) 0%, transparent 100%);
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .brand-icon-box svg {
            width: 22px;
            height: 22px;
            color: #ffffff;
        }

        .brand-info {
            flex: 1;
            min-width: 0;
        }

        .brand-info .logo-text {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-info .logo-sub {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--slate-400);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .role-badge-pill {
            display: inline-flex;
            align-items: center;
            padding: 1px 7px;
            border-radius: 9999px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .role-admin    { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3); }
        .role-kasir    { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3); }
        .role-operator { background: rgba(2, 132, 199, 0.2);  color: #7dd3fc; border: 1px solid rgba(2, 132, 199, 0.3); }

        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 18px 12px;
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--slate-800) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--slate-800);
            border-radius: 4px;
        }

        .nav-section-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--slate-500);
            padding: 14px 12px 6px;
            display: block;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--slate-300);
            text-decoration: none;
            border-radius: var(--radius-md);
            margin-bottom: 3px;
            transition: all 0.18s ease;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateX(2px);
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.25) 0%, rgba(79, 70, 229, 0.12) 100%);
            color: #ffffff;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.35);
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }

        .nav-item .nav-icon {
            font-size: 16px;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            opacity: 0.9;
            transition: transform 0.2s;
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon {
            transform: scale(1.1);
        }

        /* Sidebar Footer / User Panel */
        .sidebar-user {
            padding: 16px;
            margin: 12px;
            border-radius: var(--radius-md);
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
        }

        .user-profile-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .user-avatar-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .online-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid var(--slate-900);
            position: absolute;
            bottom: -1px;
            right: -1px;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%   { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70%  { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .user-meta {
            flex: 1;
            min-width: 0;
        }

        .user-meta .user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta .user-role-label {
            font-size: 11px;
            color: var(--slate-400);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            background: rgba(244, 63, 94, 0.1);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.25);
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #f43f5e;
            color: #ffffff;
            border-color: #f43f5e;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.35);
        }

        /* ================= MAIN CONTENT ================= */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--slate-50);
            transition: margin-left 0.28s ease;
        }

        /* Topbar */
        .topbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            padding: 0 32px;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-mobile-toggle {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            color: var(--slate-700);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
        }

        .topbar-title-group h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.4px;
            line-height: 1.2;
        }

        .topbar-breadcrumb {
            font-size: 12px;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Live Clock Widget */
        .live-clock-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            border-radius: 9999px;
            font-size: 12px;
            color: var(--slate-600);
            font-weight: 500;
        }

        .live-clock-pill .clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: var(--slate-900);
        }

        .live-status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: var(--emerald-light);
            border: 1px solid var(--emerald-border);
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--emerald-hover);
        }

        .pulse-mini {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--emerald);
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }

        /* User Profile Dropdown */
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            border: 1px solid var(--slate-200);
            border-radius: 9999px;
            background: #ffffff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-800);
            font-family: inherit;
            transition: all 0.15s ease;
        }

        .profile-btn:hover {
            border-color: var(--slate-300);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            background: var(--slate-50);
        }

        .profile-btn .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
        }

        #dropdown-profil {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            width: 220px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
            z-index: 1000;
            overflow: hidden;
            animation: slideDrop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideDrop {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .dropdown-header {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--slate-100);
            background: var(--slate-50);
        }

        .dropdown-item {
            width: 100%;
            padding: 10px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--slate-700);
            font-family: inherit;
            transition: background 0.15s, color 0.15s;
        }

        .dropdown-item:hover {
            background: var(--slate-100);
            color: var(--slate-950);
        }

        .dropdown-item.danger {
            color: var(--rose);
        }
        .dropdown-item.danger:hover {
            background: var(--rose-light);
            color: var(--rose-hover);
        }

        /* Page Content */
        .page-content {
            padding: 32px;
            flex: 1;
        }

        /* Alerts & Flash Messages */
        .flash {
            border-radius: var(--radius-md);
            padding: 14px 18px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-subtle);
        }

        .flash.success {
            background: var(--emerald-light);
            border: 1px solid var(--emerald-border);
            color: #065f46;
        }

        .flash.error {
            background: var(--rose-light);
            border: 1px solid var(--rose-border);
            color: #9f1239;
        }

        .flash.warning {
            background: var(--amber-light);
            border: 1px solid var(--amber-border);
            color: #92400e;
        }

        /* ================= CARDS ================= */
        .card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .card-header h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.2px;
        }

        .card-body {
            padding: 24px;
        }

        /* ================= STAT CARDS ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: var(--slate-300);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5 0%, #06b6d4 100%);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--slate-100);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
            border: 1px solid var(--slate-200);
        }

        .stat-card .stat-value {
            font-size: 30px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            line-height: 1.1;
            margin-bottom: 6px;
            font-feature-settings: "tnum";
        }

        .stat-card .stat-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-500);
            letter-spacing: 0.2px;
        }

        /* ================= TABLES ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        thead tr {
            background: var(--slate-50);
            border-bottom: 1px solid var(--slate-200);
        }

        thead th {
            padding: 12px 18px;
            font-size: 11px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--slate-500);
            font-weight: 700;
        }

        tbody tr {
            border-bottom: 1px solid var(--slate-100);
            transition: background 0.15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(241, 245, 249, 0.65);
        }

        tbody td {
            padding: 14px 18px;
            color: var(--slate-800);
            vertical-align: middle;
        }

        /* ================= BADGES ================= */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .badge-gold    { background: var(--amber-light);   color: #b45309; border: 1px solid var(--amber-border); }
        .badge-green   { background: var(--emerald-light); color: #047857; border: 1px solid var(--emerald-border); }
        .badge-red     { background: var(--rose-light);    color: #be123c; border: 1px solid var(--rose-border); }
        .badge-blue    { background: var(--sky-light);     color: #0369a1; border: 1px solid var(--sky-border); }
        .badge-gray    { background: var(--slate-100);     color: var(--slate-600); border: 1px solid var(--slate-200); }

        /* ================= BUTTONS ================= */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.18s ease;
            box-shadow: var(--shadow-subtle);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            border-color: #4338ca;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
        }

        .btn-outline {
            background: #ffffff;
            color: var(--slate-700);
            border-color: var(--slate-300);
        }

        .btn-outline:hover {
            background: var(--slate-50);
            border-color: var(--slate-400);
            color: var(--slate-900);
        }

        .btn-danger {
            background: var(--rose-light);
            color: var(--rose-hover);
            border-color: var(--rose-border);
        }

        .btn-danger:hover {
            background: #f43f5e;
            color: #ffffff;
            border-color: #f43f5e;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.3);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* ================= FORMS ================= */
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
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-700);
            letter-spacing: 0.2px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 14px;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            color: var(--slate-900);
            font-family: inherit;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-group .field-error {
            font-size: 12px;
            color: var(--rose-hover);
            font-weight: 500;
        }

        /* Backdrop Drawer for Mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 90;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 960px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-drawer);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main {
                margin-left: 0;
            }
            .btn-mobile-toggle {
                display: inline-flex;
            }
            .live-clock-pill {
                display: none;
            }
            .topbar {
                padding: 0 18px;
            }
            .page-content {
                padding: 20px 16px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Backdrop for Mobile Drawer --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarMobile()"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon-box">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>
        <div class="brand-info">
            <div class="logo-text">Classic Coffee</div>
            @php
                $peranUser = auth('karyawan')->user()->peran ?? 'staff';
                $roleClass = match($peranUser) {
                    'admin'    => 'role-admin',
                    'kasir'    => 'role-kasir',
                    'operator' => 'role-operator',
                    default    => 'role-admin'
                };
            @endphp
            <div class="logo-sub">
                Staff Console <span class="role-badge-pill {{ $roleClass }}">{{ $peranUser }}</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @yield('sidebar-nav')
    </nav>

    <div class="sidebar-user">
        <div class="user-profile-row">
            <div class="user-avatar-badge">
                {{ strtoupper(substr(auth('karyawan')->user()->nama ?? 'U', 0, 2)) }}
                <div class="online-dot"></div>
            </div>
            <div class="user-meta">
                <div class="user-name" title="{{ auth('karyawan')->user()->nama ?? '' }}">
                    {{ auth('karyawan')->user()->nama ?? 'Karyawan' }}
                </div>
                <div class="user-role-label">
                    <span>🟢 Online</span> • <span style="text-transform:capitalize;">{{ $peranUser }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('staff.logout') }}">
            @csrf
            <button class="btn-logout" type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Keluar Sesi
            </button>
        </form>
    </div>
</aside>

{{-- Main Content Canvas --}}
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button type="button" class="btn-mobile-toggle" onclick="toggleSidebarMobile()" title="Buka Menu">
                ☰
            </button>
            <div class="topbar-title-group">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="topbar-breadcrumb">
                    <span>Portal Karyawan</span>
                    <span>›</span>
                    <span style="color:var(--slate-800);font-weight:600;">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>
        </div>

        <div class="topbar-right">
            {{-- Live Clock Widget --}}
            <div class="live-clock-pill" title="Waktu Server Lokal">
                <span>🗓️ <span id="liveDateText">--</span></span>
                <span>•</span>
                <span class="clock-time" id="liveTimeText">00:00:00 WIB</span>
            </div>

            <div class="live-status-pill">
                <span class="pulse-mini"></span>
                <span>Shift Aktif</span>
            </div>

            {{-- Dropdown Profil --}}
            <div style="position:relative;display:inline-block;" id="profil-wrapper">
                <button type="button" class="profile-btn" onclick="toggleDropdownProfil()">
                    <span class="avatar-circle">
                        {{ strtoupper(substr(auth('karyawan')->user()->nama ?? 'U', 0, 2)) }}
                    </span>
                    <span>{{ explode(' ', trim(auth('karyawan')->user()->nama ?? 'User'))[0] }}</span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="dropdown-profil">
                    <div class="dropdown-header">
                        <div style="font-size:13px;font-weight:700;color:var(--slate-900);">
                            {{ auth('karyawan')->user()->nama ?? '' }}
                        </div>
                        <div style="font-size:11px;color:var(--slate-500);text-transform:capitalize;margin-top:2px;">
                            {{ auth('karyawan')->user()->email ?? '' }} • {{ $peranUser }}
                        </div>
                    </div>
                    
                    <button type="button" class="dropdown-item" onclick="bukaModalProfilUniversal('nama'); toggleDropdownProfil();">
                        <span>✏️</span> Ubah Nama
                    </button>
                    <button type="button" class="dropdown-item" onclick="bukaModalProfilUniversal('password'); toggleDropdownProfil();">
                        <span>🔑</span> Ganti Password
                    </button>
                    
                    <div style="border-top:1px solid var(--slate-100);">
                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                <span>🚪</span> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="page-content">
        @if (session('success'))
            <div class="flash success">
                <span style="font-size:18px;">✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="flash error">
                <span style="font-size:18px;">❌</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if (session('warning'))
            <div class="flash warning">
                <span style="font-size:18px;">⚠️</span>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Global Universal Profile Modal for all employee dashboards --}}
@include('admin.modal_profil')

<script>
    // Live Realtime Clock in Indonesian
    function updateLiveClock() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        const dayName = days[now.getDay()];
        const dayDate = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');

        const dateEl = document.getElementById('liveDateText');
        const timeEl = document.getElementById('liveTimeText');

        if (dateEl) dateEl.textContent = `${dayName}, ${dayDate} ${monthName} ${year}`;
        if (timeEl) timeEl.textContent = `${h}:${m}:${s} WIB`;
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    // Profile Dropdown Toggle
    function toggleDropdownProfil() {
        const dd = document.getElementById('dropdown-profil');
        if (dd) {
            dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
        }
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('profil-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            const dd = document.getElementById('dropdown-profil');
            if (dd) dd.style.display = 'none';
        }
    });

    // Mobile Sidebar Drawer Toggle
    function toggleSidebarMobile() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar && backdrop) {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('active');
        }
    }

    // Proxy function to open profile modal safely
    function bukaModalProfilUniversal(tab) {
        if (typeof bukaModalProfil === 'function') {
            bukaModalProfil(tab);
        }
    }
</script>

@stack('scripts')
</body>
</html>

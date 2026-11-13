<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff Portal — Classic Coffee</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --slate-950: #020617;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            
            --primary:       #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-glow:  rgba(79, 70, 229, 0.25);
            
            --emerald:       #10b981;
            --rose:          #f43f5e;
            --rose-light:    #fff1f2;
            --rose-border:   #fecdd3;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--slate-950);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ============= LEFT BRAND PANEL ============= */
        .brand-panel {
            background: radial-gradient(circle at 20% 30%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 64px;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Tech Mesh Background */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(99, 102, 241, 0.16) 1.5px, transparent 1.5px);
            background-size: 26px 26px;
            pointer-events: none;
            opacity: 0.6;
        }

        /* Glowing Radial Accent */
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.22) 0%, transparent 70%);
            top: 25%;
            left: 15%;
            pointer-events: none;
            filter: blur(40px);
        }

        .brand-top {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #a5b4fc;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }

        .brand-center {
            position: relative;
            z-index: 10;
            max-width: 520px;
            margin: auto 0;
            padding: 40px 0;
        }

        .emblem-wrapper {
            width: 76px;
            height: 76px;
            border-radius: 22px;
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: floatGlow 5s ease-in-out infinite;
        }

        @keyframes floatGlow {
            0%, 100% { transform: translateY(0); box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4); }
            50%      { transform: translateY(-8px); box-shadow: 0 18px 36px rgba(79, 70, 229, 0.55); }
        }

        .emblem-wrapper svg {
            width: 38px;
            height: 38px;
            color: #ffffff;
        }

        .brand-title {
            font-size: 38px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -1px;
            line-height: 1.15;
            margin-bottom: 14px;
        }

        .brand-title span {
            background: linear-gradient(135deg, #a5b4fc 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-desc {
            font-size: 15px;
            color: var(--slate-400);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        /* Feature Badges */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: var(--slate-300);
            font-weight: 500;
        }

        .feature-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
            flex-shrink: 0;
        }

        .brand-bottom {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--slate-500);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 24px;
        }

        /* ============= RIGHT FORM PANEL ============= */
        .form-panel {
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 56px;
            position: relative;
        }

        .form-card {
            width: 100%;
            max-width: 400px;
            animation: fadeInRight 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .form-header {
            margin-bottom: 32px;
        }

        .portal-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.6px;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 13.5px;
            color: var(--slate-500);
            line-height: 1.5;
        }

        /* Flash Messages */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: var(--rose-light);
            border: 1px solid var(--rose-border);
            color: #be123c;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        /* Inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 8px;
            letter-spacing: 0.1px;
        }

        .input-relative {
            position: relative;
        }

        .input-relative input {
            width: 100%;
            padding: 12px 44px 12px 42px;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            font-size: 14px;
            color: var(--slate-900);
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-relative input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .toggle-pw-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--slate-400);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s;
        }

        .toggle-pw-btn:hover {
            color: var(--slate-700);
        }

        /* Remember & Forgot */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--slate-600);
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid var(--slate-100);
            text-align: center;
            font-size: 12.5px;
            color: var(--slate-500);
            line-height: 1.6;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 960px) {
            body {
                grid-template-columns: 1fr;
            }
            .brand-panel {
                padding: 40px 24px;
                min-height: auto;
            }
            .brand-title {
                font-size: 28px;
            }
            .feature-list {
                display: none;
            }
            .form-panel {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

{{-- Left Brand Panel --}}
<div class="brand-panel">
    <div class="brand-top">
        <div class="brand-badge">
            <span>🛡️</span>
            <span>Enterprise Staff Console</span>
        </div>
        <div style="font-size:12px;color:var(--slate-400);font-weight:600;">
            v2.5 Modern
        </div>
    </div>

    <div class="brand-center">
        <div class="emblem-wrapper">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>

        <h1 class="brand-title">
            Classic Coffee 789<br>
            <span>Staff Portal</span>
        </h1>
        <p class="brand-desc">
            Sistem terintegrasi untuk pengelolaan transaksi kasir, inventori stok operator, dan kendali administratif dalam satu platform yang aman.
        </p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon-box">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <span>Role-Based Access Control (Admin, Kasir, & Operator)</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon-box">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span>Automated POS & Live Order Tracking System</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon-box">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span>Enkripsi 256-bit & Proteksi Sesi Kerja Realtime</span>
            </div>
        </div>
    </div>

    <div class="brand-bottom">
        <div>© 2026 Classic Coffee 789. All rights reserved.</div>
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#10b981;"></span>
            <span>Server Live • France Edge</span>
        </div>
    </div>
</div>

{{-- Right Form Panel --}}
<div class="form-panel">
    <div class="form-card">
        <div class="form-header">
            <div class="portal-eyebrow">
                <span>🔐</span> Internal Access
            </div>
            <h2>Selamat Datang 👋</h2>
            <p>Masukkan kredensial akun staff Anda untuk mengakses dashboard.</p>
        </div>

        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="alert-box alert-success">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="alert-box alert-error">
                <span>❌</span>
                <div>{{ session('error') ?? $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Alamat Email Staff</label>
                <div class="input-relative">
                    <span class="input-icon-left">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@classiccoffee.id"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password Akun</label>
                <div class="input-relative">
                    <span class="input-icon-left">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-pw-btn" onclick="togglePasswordVisibility()" title="Lihat/Sembunyikan Password">
                        <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <label class="checkbox-label" for="remember">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <a href="{{ route('staff.password.request') }}" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-submit">
                <span>Masuk ke Dashboard</span>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <div class="form-footer">
            Belum mengatur password atau lupa kredensial?<br>
            Cek email undangan aktivasi atau hubungi <strong>Super Admin</strong>.
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
        }
    }
</script>

</body>
</html>

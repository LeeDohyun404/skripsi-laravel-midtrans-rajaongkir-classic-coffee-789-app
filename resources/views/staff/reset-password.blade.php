<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password — Classic Coffee Staff</title>
    
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

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(99, 102, 241, 0.16) 1.5px, transparent 1.5px);
            background-size: 26px 26px;
            pointer-events: none;
            opacity: 0.6;
        }

        .brand-top {
            position: relative;
            z-index: 10;
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

        .brand-desc {
            font-size: 15px;
            color: var(--slate-400);
            line-height: 1.7;
        }

        .brand-bottom {
            position: relative;
            z-index: 10;
            font-size: 12px;
            color: var(--slate-500);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 24px;
        }

        .form-panel {
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 56px;
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
            margin-bottom: 28px;
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

        .alert-error {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--rose-light);
            border: 1px solid var(--rose-border);
            color: #be123c;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 7px;
        }

        .input-relative {
            position: relative;
        }

        .input-relative input {
            width: 100%;
            padding: 11px 44px 11px 16px;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            font-size: 14px;
            color: var(--slate-900);
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        .input-relative input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .toggle-pw {
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
        }

        .strength-bar {
            height: 4px;
            background: var(--slate-200);
            border-radius: 2px;
            margin-top: 7px;
            overflow: hidden;
        }

        .strength-bar .fill {
            height: 100%;
            width: 0;
            transition: width 0.3s, background 0.3s;
            border-radius: 2px;
        }

        .strength-label {
            font-size: 11px;
            color: var(--slate-500);
            margin-top: 4px;
            font-weight: 600;
        }

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
            margin-top: 6px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            color: var(--slate-500);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        @media (max-width: 960px) {
            body {
                grid-template-columns: 1fr;
            }
            .brand-panel {
                padding: 40px 24px;
            }
            .form-panel {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

<div class="brand-panel">
    <div class="brand-top">
        <div class="brand-badge">
            <span>🛡️</span>
            <span>Security Management</span>
        </div>
    </div>

    <div class="brand-center">
        <div class="emblem-wrapper">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h1 class="brand-title">Atur Ulang Kata Sandi</h1>
        <p class="brand-desc">
            Buat kata sandi baru yang kuat untuk menjaga keamanan akun dan operasional Classic Coffee 789.
        </p>
    </div>

    <div class="brand-bottom">
        © 2026 Classic Coffee 789. All rights reserved.
    </div>
</div>

<div class="form-panel">
    <div class="form-card">
        <div class="form-header">
            <div class="portal-eyebrow">
                <span>🔐</span> Kredensial Baru
            </div>
            <h2>Password Baru</h2>
            <p>Masukkan kata sandi baru untuk akun Anda.</p>
        </div>

        @if (session('error') || $errors->any())
            <div class="alert-error">
                <span>❌</span>
                <div>{{ session('error') ?? $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label>Alamat Email</label>
                <input
                    type="email"
                    value="{{ $email }}"
                    readonly
                    style="width: 100%; padding: 11px 16px; background: var(--slate-100); border: 1.5px solid var(--slate-200); border-radius: 12px; font-size: 13.5px; color: var(--slate-500); cursor: not-allowed; font-family: inherit;"
                >
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter kombinasi"
                        autocomplete="new-password"
                        required
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw('password')">👁</button>
                </div>
                <div class="strength-bar"><div class="fill" id="strength-fill"></div></div>
                <div class="strength-label" id="strength-label"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="input-relative">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ketik ulang password baru"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation')">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Simpan Password Baru</span>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <div>
            <a href="{{ route('staff.login') }}" class="back-link">
                <span>←</span> Kembali ke Halaman Login
            </a>
        </div>
    </div>
</div>

<script>
    function togglePw(fieldId) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    function checkStrength(value) {
        const fill  = document.getElementById('strength-fill');
        const label = document.getElementById('strength-label');

        let score = 0;
        if (value.length >= 8)  score++;
        if (value.length >= 12) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const levels = [
            { width: '0%',   color: 'transparent', text: '' },
            { width: '25%',  color: '#f43f5e',     text: 'Terlalu lemah' },
            { width: '50%',  color: '#f59e0b',     text: 'Cukup' },
            { width: '75%',  color: '#0284c7',     text: 'Kuat' },
            { width: '100%', color: '#10b981',     text: 'Sangat kuat & aman' },
        ];

        const lvl = levels[Math.min(score, 4)];
        fill.style.width = lvl.width;
        fill.style.background = lvl.color;
        label.textContent = lvl.text;
        label.style.color = lvl.color;
    }
</script>

</body>
</html>

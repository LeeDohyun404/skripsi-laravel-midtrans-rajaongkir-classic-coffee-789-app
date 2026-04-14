<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun — Classic Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
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
            --error:       #c0392b;
            --success:     #2e7d52;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--brown-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Background texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 80%, rgba(107,63,31,0.4) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(61,36,16,0.6) 0%, transparent 50%);
            pointer-events: none;
        }

        body::after {
            content: '☕';
            position: fixed;
            font-size: 300px;
            opacity: 0.03;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            user-select: none;
        }

        /* Card */
        .card {
            position: relative;
            background: var(--cream);
            border-radius: 20px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(200,169,110,0.2);
            animation: slideUp 0.6s cubic-bezier(0.16,1,0.3,1);
            margin: 32px 0;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Card header */
        .card-header {
            background: var(--brown-mid);
            padding: 32px 40px 28px;
            text-align: center;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .brand-mark {
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 10px;
        }

        .card-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--cream);
            font-weight: 400;
        }

        .card-header .subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* Welcome strip */
        .welcome-strip {
            background: var(--gold-light);
            padding: 14px 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(200,169,110,0.3);
        }

        .welcome-strip .avatar {
            width: 36px;
            height: 36px;
            background: var(--brown-mid);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .welcome-strip .info {
            min-width: 0;
        }

        .welcome-strip .name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .welcome-strip .role {
            font-size: 11px;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Form body */
        .card-body {
            padding: 32px 40px 36px;
        }

        .section-label {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* Field group */
        .field-group {
            margin-bottom: 20px;
        }

        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--brown-warm);
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 44px 12px 16px;
            background: white;
            border: 1.5px solid var(--cream-dark);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200,169,110,0.15);
        }

        .input-wrapper input.error {
            border-color: var(--error);
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 16px;
            padding: 0;
            line-height: 1;
        }

        .field-error {
            font-size: 12px;
            color: var(--error);
            margin-top: 5px;
        }

        /* Password strength */
        .strength-bar {
            margin-top: 10px;
        }

        .strength-track {
            height: 4px;
            background: var(--cream-dark);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }

        .strength-text {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Requirements */
        .requirements {
            background: var(--cream-dark);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }

        .req-title {
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .req-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 6px;
            transition: color 0.2s;
        }

        .req-item:last-child { margin-bottom: 0; }

        .req-item .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--cream-dark);
            border: 1.5px solid var(--text-muted);
            flex-shrink: 0;
            transition: background 0.2s, border-color 0.2s;
        }

        .req-item.met {
            color: var(--success);
        }

        .req-item.met .dot {
            background: var(--success);
            border-color: var(--success);
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--brown-mid);
            color: var(--gold);
            border: 2px solid var(--gold);
            border-radius: 10px;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Playfair Display', serif;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: var(--gold);
            color: var(--brown-mid);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Flash error */
        .flash-error {
            background: #fdf0ef;
            border: 1px solid #f5c0bc;
            border-left: 4px solid var(--error);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: var(--error);
            margin-bottom: 20px;
        }

        /* Footer note */
        .card-footer {
            padding: 0 40px 28px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.7;
        }
    </style>
</head>
<body>

<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="brand-mark">☕ Classic Coffee</div>
        <h1>Aktivasi Akun</h1>
        <div class="subtitle">Buat password untuk melanjutkan</div>
    </div>

    {{-- Welcome strip --}}
    <div class="welcome-strip">
        <div class="avatar">👤</div>
        <div class="info">
            <div class="name">{{ $karyawan->nama }}</div>
            <div class="role">{{ ucfirst($karyawan->peran) }} · {{ $karyawan->email }}</div>
        </div>
    </div>

    {{-- Body --}}
    <div class="card-body">

        {{-- Flash error --}}
        @if ($errors->any())
            <div class="flash-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="flash-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="section-label">Buat Password Baru</div>

        <form method="POST" action="{{ request()->fullUrl() }}" id="aktivasiForm">
            @csrf

            {{-- Password --}}
            <div class="field-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password"
                        class="{{ $errors->has('password') ? 'error' : '' }}"
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw('password', this)">👁</button>
                </div>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror

                {{-- Strength bar --}}
                <div class="strength-bar">
                    <div class="strength-track">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Belum diisi</div>
                </div>
            </div>

            {{-- Confirm password --}}
            <div class="field-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation', this)">👁</button>
                </div>
            </div>

            {{-- Requirements --}}
            <div class="requirements">
                <div class="req-title">Syarat Password</div>
                <div class="req-item" id="req-length">
                    <div class="dot"></div> Minimal 8 karakter
                </div>
                <div class="req-item" id="req-upper">
                    <div class="dot"></div> Mengandung huruf kapital
                </div>
                <div class="req-item" id="req-number">
                    <div class="dot"></div> Mengandung angka
                </div>
                <div class="req-item" id="req-special">
                    <div class="dot"></div> Mengandung karakter khusus (!@#$...)
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                Aktivasi & Masuk →
            </button>
        </form>
    </div>

    <div class="card-footer">
        Link aktivasi berlaku 24 jam.<br>
        Hubungi admin jika ada kendala.
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }

    function checkStrength(val) {
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');

        const checks = {
            length:  val.length >= 8,
            upper:   /[A-Z]/.test(val),
            number:  /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val),
        };

        // Update requirement items
        document.getElementById('req-length').classList.toggle('met', checks.length);
        document.getElementById('req-upper').classList.toggle('met', checks.upper);
        document.getElementById('req-number').classList.toggle('met', checks.number);
        document.getElementById('req-special').classList.toggle('met', checks.special);

        const score = Object.values(checks).filter(Boolean).length;

        const levels = [
            { width: '0%',   color: '#e0d6c8', label: 'Belum diisi' },
            { width: '25%',  color: '#c0392b', label: 'Lemah' },
            { width: '50%',  color: '#e67e22', label: 'Sedang' },
            { width: '75%',  color: '#f1c40f', label: 'Bagus' },
            { width: '100%', color: '#2e7d52', label: 'Kuat 💪' },
        ];

        const idx = val.length === 0 ? 0 : score;
        fill.style.width = levels[idx].width;
        fill.style.background = levels[idx].color;
        text.textContent = levels[idx].label;
        text.style.color = levels[idx].color === '#e0d6c8' ? '#9e7a4a' : levels[idx].color;
    }
</script>

</body>
</html>
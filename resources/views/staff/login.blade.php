<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff — Classic Coffee</title>
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
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--brown-dark);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* Left panel — branding */
        .panel-brand {
            background: linear-gradient(160deg, #3d2410 0%, #1e1108 60%, #0e0804 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        .panel-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 70%, rgba(200,169,110,0.08) 0%, transparent 60%);
        }

        .panel-brand .big-icon {
            font-size: 100px;
            margin-bottom: 32px;
            filter: drop-shadow(0 10px 30px rgba(200,169,110,0.3));
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        .panel-brand .eyebrow {
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 16px;
        }

        .panel-brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: var(--cream);
            font-weight: 400;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .panel-brand p {
            font-size: 14px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.8;
            max-width: 280px;
        }

        .panel-brand .divider {
            width: 60px;
            height: 1px;
            background: var(--gold);
            margin: 24px auto;
            opacity: 0.4;
        }

        /* Right panel — form */
        .panel-form {
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 56px;
        }

        .form-container {
            width: 100%;
            max-width: 360px;
            animation: slideIn 0.5s cubic-bezier(0.16,1,0.3,1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(24px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header .label {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--text);
            font-weight: 400;
        }

        /* Flash messages */
        .flash {
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .flash.error {
            background: #fdf0ef;
            border: 1px solid #f5c0bc;
            border-left: 4px solid var(--error);
            color: var(--error);
        }

        .flash.success {
            background: #edf7f1;
            border: 1px solid #a8dabb;
            border-left: 4px solid #2e7d52;
            color: #2e7d52;
        }

        /* Fields */
        .field-group {
            margin-bottom: 18px;
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
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-wrapper input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200,169,110,0.15);
        }

        .input-wrapper .icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-muted);
            padding: 0;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--brown-mid);
            cursor: pointer;
        }

        .remember-row label {
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* Submit */
        .btn-login {
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

        .btn-login:hover {
            background: var(--gold);
            color: var(--brown-mid);
        }

        .btn-login:active { transform: scale(0.98); }

        .form-footer {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .panel-brand { display: none; }
            .panel-form { padding: 40px 24px; }
        }
    </style>
</head>
<body>

{{-- Left panel --}}
<div class="panel-brand">
    <div class="big-icon">☕</div>
    <div class="eyebrow">Sistem Internal</div>
    <h1>Classic<br>Coffee</h1>
    <div class="divider"></div>
    <p>Portal khusus karyawan. Silakan masuk dengan kredensial yang diberikan admin.</p>
</div>

{{-- Right panel --}}
<div class="panel-form">
    <div class="form-container">

        <div class="form-header">
            <div class="label">Staff Portal</div>
            <h2>Selamat<br>Datang 👋</h2>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flash success">✅ {{ session('success') }}</div>
        @endif

        @if (session('error') || $errors->any())
            <div class="flash error">
                ❌ {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('staff.login.post') }}">
            @csrf

            <div class="field-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@classiccoffee.id"
                        autocomplete="email"
                        required
                    >
                    <span class="icon">✉️</span>
                </div>
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw()">👁</button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn-login">Masuk →</button>
        </form>

        <div class="form-footer">
            Belum mengatur password?<br>
            Cek email undangan Anda atau hubungi admin.
        </div>

    </div>
</div>

<script>
    function togglePw() {
        const input = document.getElementById('password');
        const btn = document.querySelector('.toggle-pw');
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }
</script>

</body>
</html>

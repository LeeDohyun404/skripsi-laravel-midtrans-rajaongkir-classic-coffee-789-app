<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Bergabung — Classic Coffee</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Georgia', serif;
            background-color: #f5f0e8;
            color: #2c1a0e;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #faf7f2;
        }

        /* Header */
        .header {
            background-color: #2c1a0e;
            padding: 40px 48px;
            text-align: center;
        }

        .header .logo-mark {
            font-size: 13px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #c8a96e;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 28px;
            color: #faf7f2;
            font-weight: normal;
            letter-spacing: 1px;
        }

        .header .tagline {
            font-size: 12px;
            color: #9e7a4a;
            letter-spacing: 2px;
            margin-top: 6px;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #3d2410 0%, #6b3f1f 50%, #8b5a2b 100%);
            padding: 48px 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '☕';
            position: absolute;
            font-size: 120px;
            opacity: 0.06;
            top: -20px;
            right: -20px;
        }

        .hero .greeting {
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #c8a96e;
            margin-bottom: 16px;
        }

        .hero h2 {
            font-size: 26px;
            color: #faf7f2;
            font-weight: normal;
            line-height: 1.4;
            margin-bottom: 12px;
        }

        .hero h2 span {
            color: #c8a96e;
            font-style: italic;
        }

        .hero p {
            font-size: 14px;
            color: #c4a882;
            line-height: 1.7;
            max-width: 420px;
            margin: 0 auto;
        }

        /* Divider */
        .divider {
            height: 3px;
            background: linear-gradient(90deg, transparent, #c8a96e, transparent);
            margin: 0;
        }

        /* Body */
        .body {
            padding: 40px 48px;
        }

        /* Credential card */
        .credential-card {
            background: #2c1a0e;
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 32px;
            border: 1px solid #3d2410;
        }

        .credential-card .card-title {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #c8a96e;
            margin-bottom: 20px;
        }

        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #3d2410;
        }

        .credential-row:last-child {
            border-bottom: none;
        }

        .credential-row .label {
            font-size: 12px;
            color: #9e7a4a;
            letter-spacing: 1px;
        }

        .credential-row .value {
            font-size: 14px;
            color: #faf7f2;
            font-family: 'Courier New', monospace;
        }

        .credential-row .value.role-badge {
            background: #c8a96e;
            color: #2c1a0e;
            padding: 3px 10px;
            border-radius: 20px;
            font-family: 'Georgia', serif;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: capitalize;
        }

        /* Steps */
        .steps-title {
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #8b5a2b;
            margin-bottom: 20px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #c8a96e;
            color: #2c1a0e;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-content .step-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c1a0e;
            margin-bottom: 4px;
        }

        .step-content .step-desc {
            font-size: 13px;
            color: #7a5c3a;
            line-height: 1.6;
        }

        /* CTA Button */
        .cta-wrapper {
            text-align: center;
            margin: 36px 0 28px;
        }

        .cta-button {
            display: inline-block;
            background: #2c1a0e;
            color: #c8a96e !important;
            text-decoration: none;
            padding: 16px 40px;
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            border-radius: 4px;
            border: 2px solid #c8a96e;
            font-family: 'Georgia', serif;
        }

        .cta-link {
            text-align: center;
            margin-top: 12px;
        }

        .cta-link a {
            font-size: 11px;
            color: #9e7a4a;
            word-break: break-all;
        }

        /* Expiry warning */
        .expiry-notice {
            background: #fff8ee;
            border: 1px solid #e8d5a3;
            border-left: 4px solid #c8a96e;
            border-radius: 6px;
            padding: 14px 18px;
            font-size: 13px;
            color: #6b4c24;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .expiry-notice strong {
            color: #2c1a0e;
        }

        /* Footer */
        .footer {
            background: #2c1a0e;
            padding: 28px 48px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #6b4c24;
            line-height: 1.8;
        }

        .footer .brand {
            color: #c8a96e;
            font-style: italic;
            font-size: 14px;
            display: block;
            margin-bottom: 8px;
        }

        .footer .ignore-note {
            margin-top: 12px;
            font-size: 11px;
            color: #4a2e14;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <div class="logo-mark">☕ Classic Coffee</div>
            <h1>Selamat Datang di Tim Kami</h1>
            <div class="tagline">Since the beginning · Quality above all</div>
        </div>

        {{-- Hero --}}
        <div class="hero">
            <div class="greeting">Undangan Bergabung</div>
            <h2>Halo, <span>{{ $karyawan->nama }}</span>! 👋</h2>
            <p>
                Anda telah ditambahkan sebagai karyawan Classic Coffee.
                Lengkapi akun Anda dan mulai perjalanan bersama kami.
            </p>
        </div>

        <div class="divider"></div>

        {{-- Body --}}
        <div class="body">

            {{-- Credential Card --}}
            <div class="credential-card">
                <div class="card-title">📋 Detail Akun Anda</div>

                <div class="credential-row">
                    <span class="label">Nama Lengkap</span>
                    <span class="value">{{ $karyawan->nama }}</span>
                </div>

                <div class="credential-row">
                    <span class="label">Email Login</span>
                    <span class="value">{{ $karyawan->email }}</span>
                </div>

                <div class="credential-row">
                    <span class="label">Peran</span>
                    <span class="value role-badge">{{ ucfirst($karyawan->peran) }}</span>
                </div>
            </div>

            {{-- Steps --}}
            <div class="steps-title">Cara Aktivasi Akun</div>

            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <div class="step-title">Klik Tombol Aktivasi</div>
                    <div class="step-desc">Tekan tombol di bawah untuk membuka halaman aktivasi akun Anda.</div>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <div class="step-title">Buat Password</div>
                    <div class="step-desc">Buat password baru yang kuat dan mudah Anda ingat. Password minimal 8 karakter.</div>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <div class="step-title">Login & Mulai Bekerja</div>
                    <div class="step-desc">Setelah aktivasi berhasil, Anda akan diarahkan ke halaman login staff.</div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="cta-wrapper">
                <a href="{{ $urlAktivasi }}" class="cta-button">Aktivasi Akun Saya</a>
            </div>
            <div class="cta-link">
                <p style="font-size:11px;color:#9e7a4a;margin-bottom:4px;">Atau salin link ini ke browser:</p>
                <a href="{{ $urlAktivasi }}">{{ $urlAktivasi }}</a>
            </div>

            {{-- Expiry Notice --}}
            <div class="expiry-notice" style="margin-top:24px;">
                ⏳ <strong>Perhatian:</strong> Link aktivasi ini hanya berlaku selama
                <strong>24 jam</strong> sejak email ini diterima.
                Jika sudah kedaluwarsa, hubungi admin untuk mengirim ulang undangan.
            </div>

        </div>

        {{-- Footer --}}
        <div class="footer">
            <span class="brand">Classic Coffee</span>
            <p>
                Email ini dikirim otomatis oleh sistem karena akun Anda baru saja dibuat.<br>
                Jangan membalas email ini langsung.
            </p>
            <p class="ignore-note">
                Jika Anda merasa tidak mendaftarkan diri atau tidak mengenal Classic Coffee,
                abaikan saja email ini.
            </p>
        </div>

    </div>
</body>
</html>

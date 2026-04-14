<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Valid — Classic Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #1e1108;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
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
        }

        .card {
            background: #faf7f2;
            border-radius: 20px;
            max-width: 420px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
            text-align: center;
            animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-top {
            background: #3d2410;
            padding: 36px 40px 28px;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }

        .card-top h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #faf7f2;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .card-top p {
            font-size: 13px;
            color: #9e7a4a;
            line-height: 1.7;
        }

        .card-body {
            padding: 32px 40px;
        }

        .reason-box {
            background: #f0ead8;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            text-align: left;
        }

        .reason-box .reason-title {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #9e7a4a;
            margin-bottom: 10px;
        }

        .reason-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #6b3f1f;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .reason-item:last-child { margin-bottom: 0; }
        .reason-item .bullet { flex-shrink: 0; color: #c8a96e; }

        .info-text {
            font-size: 13px;
            color: #9e7a4a;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .info-text strong { color: #3d2410; }

        .btn-back {
            display: inline-block;
            background: #3d2410;
            color: #c8a96e;
            text-decoration: none;
            padding: 13px 32px;
            border-radius: 8px;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Playfair Display', serif;
            border: 2px solid #c8a96e;
            transition: background 0.2s, color 0.2s;
        }

        .btn-back:hover {
            background: #c8a96e;
            color: #3d2410;
        }

        .card-footer {
            padding: 0 40px 28px;
            font-size: 12px;
            color: #9e7a4a;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-top">
        <span class="icon">⏳</span>
        <h1>Link Tidak Valid</h1>
        <p>Link aktivasi yang Anda gunakan sudah tidak bisa dipakai.</p>
    </div>

    <div class="card-body">
        <div class="reason-box">
            <div class="reason-title">Kemungkinan penyebab</div>
            <div class="reason-item">
                <span class="bullet">·</span>
                Link sudah kedaluwarsa (berlaku 24 jam saja)
            </div>
            <div class="reason-item">
                <span class="bullet">·</span>
                Link sudah pernah digunakan sebelumnya
            </div>
            <div class="reason-item">
                <span class="bullet">·</span>
                Link tidak lengkap (terpotong saat copy-paste)
            </div>
        </div>

        <p class="info-text">
            Hubungi <strong>admin</strong> untuk meminta pengiriman ulang
            undangan ke email Anda.
        </p>

        <a href="{{ route('staff.login') }}" class="btn-back">← Ke Halaman Login</a>
    </div>

    <div class="card-footer">
        Classic Coffee · Sistem Internal
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; margin: auto; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center fw-bold mb-4">Login Staff</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('staff.login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Masuk Sekarang</button>
                </form>
                <div class="text-center mt-3">
                    <small><a href="/" class="text-decoration-none text-muted"> Kembali ke Toko</a></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
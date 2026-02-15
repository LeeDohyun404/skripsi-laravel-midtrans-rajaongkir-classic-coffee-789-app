<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kopi 789</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md bg-white p-8 rounded-[2rem] shadow-2xl border border-stone-200">
        <h2 class="text-2xl font-black text-stone-900 mb-6 text-center tracking-tighter uppercase">Admin Access</h2>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black uppercase text-stone-500 mb-1">Username</label>
                <input type="text" name="username" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-stone-900" placeholder="Masukkan Username" required>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-stone-500 mb-1">Password</label>
                <input type="password" name="password" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-stone-900" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="w-full bg-stone-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest mt-4 hover:bg-stone-800 transition-all">
                Masuk Admin
            </button>
        </form>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - Classic 789</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-stone-100 font-sans">

    <nav class="bg-stone-900 text-white py-4 px-8 sticky top-0 z-50 shadow-2xl">
        <div class="max-w-[1600px] mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fas fa-coffee text-amber-500 text-2xl"></i>
                <h1 class="font-bold text-lg tracking-tighter uppercase">Admin <span class="text-amber-500">Classic 789</span></h1>
            </div>
            <div class="flex items-center gap-6">
                <a href="/admin" class="text-[10px] font-black uppercase tracking-widest text-stone-400 hover:text-white transition pb-1">
                    <i class="fas fa-box mr-1"></i> Produk
                </a>
                <a href="/admin/history" class="text-[10px] font-black uppercase tracking-widest text-stone-400 hover:text-white transition pb-1">
                    <i class="fas fa-history mr-1"></i> History Pesanan
                </a>
                 <a href="/admin/pelanggan" class="text-[10px] font-black uppercase tracking-widest text-white hover:text-amber-500 transition border-b-2 border-amber-500 pb-1">
                    <i class="fas fa-users mr-1"></i> Kelola Pelanggan
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-[1600px] mx-auto px-8 py-10">
        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-stone-200 overflow-hidden">
            <div class="p-8 border-b border-stone-100 bg-stone-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tighter text-stone-800">Data Pelanggan</h3>
                    <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest mt-1">Total Terdaftar: {{ count($customers) }} User</p>
                </div>
                <div class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl text-xs font-bold">
                    <i class="fas fa-user-check mr-2"></i> Database Aktif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-stone-50 text-[10px] uppercase font-black text-stone-400 border-b">
                        <tr>
                            <th class="px-8 py-5">ID</th>
                            <th class="px-8 py-5">Nama Lengkap</th>
                            <th class="px-8 py-5">Email</th>
                            <th class="px-8 py-5">Tanggal Join</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm italic">
                        @foreach($customers as $c)
                        <tr class="hover:bg-stone-50/80 transition-all group">
                            <td class="px-8 py-5 font-bold text-stone-400 not-italic">#{{ $loop->iteration }}</td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-stone-100 rounded-full flex items-center justify-center text-stone-400 group-hover:bg-amber-500 group-hover:text-white transition">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <span class="font-bold text-stone-800 not-italic uppercase tracking-tighter">{{ $c->username ?? $c->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-stone-500">{{ $c->email ?? 'N/A' }}</td>
                            <td class="px-8 py-5 text-stone-400 text-xs">{{ date('d M Y', strtotime($c->created_at ?? now())) }}</td>
                            <td class="px-8 py-5">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
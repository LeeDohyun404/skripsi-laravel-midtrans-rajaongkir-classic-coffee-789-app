<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Transaksi - Classic 789</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-stone-100 font-sans overflow-x-hidden">
    <nav class="bg-stone-900 text-white py-4 px-8 sticky top-0 z-50 shadow-2xl">
        <div class="max-w-[1600px] mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fas fa-coffee text-amber-500 text-2xl"></i>
                <h1 class="font-bold text-lg tracking-tighter uppercase">Admin <span class="text-amber-500">Classic 789</span></h1>
            </div>
            <div class="flex gap-6 text-[10px] font-black uppercase tracking-widest">
                <a href="/admin" class="hover:text-amber-500 transition">Kelola Produk</a>
                <a href="/admin/history" class="text-amber-500 border-b-2 border-amber-500 pb-1">History Transaksi</a>
                 <a href="/admin/pelanggan" class="text-[10px] font-black uppercase tracking-widest text-stone-400 hover:text-white transition pb-1">
                    <i class="fas fa-users mr-1"></i> Kelola Pelanggan
                </a>
                <a href="/produk" target="_blank" class="hover:text-amber-500 transition">Lihat Toko</a>
            </div>
        </div>
    </nav>

    <div class="max-w-[1600px] mx-auto px-4 lg:px-8 py-8">
        
        <div class="bg-white rounded-[2rem] shadow-2xl border border-stone-200 overflow-hidden">
            <div class="p-6 border-b border-stone-100 bg-stone-50/50 flex justify-between items-center">
                <h3 class="text-sm font-black uppercase tracking-widest text-stone-500">Riwayat Pesanan Masuk</h3>
                <span class="text-[10px] text-stone-400">Total: {{ count($orders) }} Transaksi</span>
            </div>
            
            <div class="overflow-x-auto">
                <div class="overflow-x-auto">
             <table class="w-full text-left border-collapse">
                <thead class="bg-stone-900 text-white text-[10px] uppercase font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Order ID & Waktu</th>
                        <th class="px-6 py-4">Detail Produk</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Pengambilan & Alamat</th>
                        <th class="px-6 py-4">Total & Bayar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @foreach($orders as $o)
                <tr class="hover:bg-amber-50/50 transition-all border-b border-stone-100">
                    <td class="px-6 py-4">
                        <span class="block font-black text-amber-600">#{{ substr($o->number, -8) }}</span>
                        <span class="text-[10px] text-stone-400 font-bold italic uppercase">
                            <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($o->created_at)->format('d M, H:i') }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <span class="font-bold text-stone-800 block italic">Pesanan Kopi</span>
                            <span class="inline-block bg-stone-900 text-white px-2 py-0.5 rounded text-[10px] font-black mt-1">
                                <i class="fas fa-box mr-1 text-amber-500"></i> {{ $o->quantity ?? '1' }} ITEM
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-black text-stone-800 text-sm">{{ $o->customer_name }}</span>
                            <span class="text-[10px] text-green-600 font-bold flex items-center">
                                <i class="fab fa-whatsapp mr-1"></i> {{ $o->customer_phone }}
                            </span>
                            <span class="text-[10px] text-stone-400 font-medium flex items-center">
                                <i class="far fa-envelope mr-1"></i> {{ $o->customer_email }}
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                    <div class="mb-2">
                        {{-- Kita tambah ?? '' biar kalau datanya kosong gak bikin error 500 --}}
                        @if(($o->order_type ?? '') == 'dine-in')
                            <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase bg-blue-100 text-blue-700 border border-blue-200">
                                <i class="fas fa-chair mr-1"></i> MAKAN DI SINI
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase bg-orange-100 text-orange-700 border border-orange-200">
                                <i class="fas fa-motorcycle mr-1"></i> BAWA PULANG
                            </span>
                        @endif
                    </div>
                    
                    {{-- Logic Alamat: kalau dine-in tulis 'Meja', kalau take-away tulis alamatnya --}}
                    <p class="text-[10px] text-stone-600 leading-tight mb-1">
                        <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> 
                        {{ ($o->order_type ?? '') == 'dine-in' ? 'Pesanan di Tempat' : ($o->address ?? 'Alamat tidak diisi') }}
                    </p>

                    @if($o->notes)
                        <p class="text-[9px] text-amber-700 italic border-l-2 border-amber-300 pl-2">
                            "{{ $o->notes }}"
                        </p>
                    @endif
                </td>

                    <td class="px-6 py-4 font-black text-stone-800">Rp {{ number_format($o->total_price) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ in_array($o->status, ['success','settlement','capture']) ? 'bg-green-500 text-white' : 'bg-amber-400 text-stone-900' }}">
                            {{ $o->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
            </div>
        </div>
    </div>
</body>
</html>
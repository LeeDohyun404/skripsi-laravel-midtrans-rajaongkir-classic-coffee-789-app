<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Classic 789</title>
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

        <div class="flex items-center gap-6">
            <a href="/admin" class="text-[10px] font-black uppercase tracking-widest text-white hover:text-amber-500 transition border-b-2 border-amber-500 pb-1">
                <i class="fas fa-box mr-1"></i> Produk
            </a>
            <a href="/admin/history" class="text-[10px] font-black uppercase tracking-widest text-stone-400 hover:text-white transition pb-1">
                <i class="fas fa-history mr-1"></i> History Pesanan
            </a>
            <a href="/admin/pelanggan" class="text-[10px] font-black uppercase tracking-widest text-stone-400 hover:text-white transition pb-1">
                <i class="fas fa-users mr-1"></i> Kelola Pelanggan
            </a>
            
            <form action="/admin/logout" method="POST" class="ml-4">
                @csrf
                <button type="submit" class="text-[10px] font-black uppercase tracking-widest bg-red-600/10 text-red-500 px-4 py-2 rounded-lg hover:bg-red-600 hover:text-white transition">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

    <div class="max-w-[1600px] mx-auto px-4 lg:px-8 py-8">
        
        @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-2xl mb-8 shadow-lg flex items-center gap-3 animate-bounce">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
            
            <div class="xl:col-span-1">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 shadow-2xl border border-stone-200 sticky top-28">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-black text-stone-800 uppercase tracking-tighter" id="form-title">Tambah Produk</h2>
                        <span id="mode-badge" class="bg-stone-100 text-stone-400 text-[9px] px-3 py-1 rounded-full font-bold">CREATE MODE</span>
                    </div>
                    
                    <form action="/admin/simpan" method="POST" enctype="multipart/form-data" id="main-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="id" id="prod-id">
                        
                        <div>
                            <label class="block text-[9px] font-black text-stone-400 uppercase tracking-[0.2em] mb-2">Nama Menu</label>
                            <input type="text" name="name" id="prod-name" placeholder="Contoh: Hazelnut Latte" required 
                                class="w-full px-4 py-3 rounded-xl bg-stone-50 border border-stone-200 focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-stone-400 uppercase tracking-[0.2em] mb-2">Harga Jual (Rp)</label>
                            <input type="number" name="price" id="prod-price" placeholder="25000" required 
                                class="w-full px-4 py-3 rounded-xl bg-stone-50 border border-stone-200 focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-stone-400 uppercase tracking-[0.2em] mb-2">Kategori</label>
                            <select name="category" id="prod-cat" class="w-full px-4 py-3 rounded-xl bg-stone-50 border border-stone-200 outline-none cursor-pointer">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="paket">Paket Hemat</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-stone-400 uppercase tracking-[0.2em] mb-2">Unggah Foto</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col w-full h-20 border-2 border-dashed border-stone-200 hover:bg-stone-50 hover:border-amber-300 rounded-xl cursor-pointer transition-all">
                                    <div class="flex flex-col items-center justify-center pt-3">
                                        <i class="fas fa-image text-stone-300 mb-1"></i>
                                        <p class="text-[10px] text-stone-400">Klik untuk upload gambar</p>
                                    </div>
                                    <input type="file" name="image" class="hidden" />
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col gap-2">
                            <button type="submit" id="btn-submit" class="w-full bg-stone-900 text-white font-bold py-4 rounded-xl hover:bg-amber-700 transition shadow-xl uppercase text-[10px] tracking-[0.2em]">Simpan Produk</button>
                            <button type="button" onclick="resetForm()" id="btn-reset" class="hidden w-full bg-stone-200 text-stone-600 font-bold py-3 rounded-xl hover:bg-stone-300 transition uppercase text-[10px] tracking-[0.2em]">Batal / Tambah Baru</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="xl:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-2xl border border-stone-200 overflow-hidden">
                    <div class="p-6 border-b border-stone-100 bg-stone-50/50 flex justify-between items-center">
                        <h3 class="text-sm font-black uppercase tracking-widest text-stone-500">Daftar Menu Tersedia</h3>
                        <span class="text-[10px] text-stone-400">Total: {{ count($products) }} Produk</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-stone-50 text-[9px] uppercase font-black text-stone-400 border-b">
                                <tr>
                                    <th class="px-4 py-4">Menu & Gambar</th>
                                    <th class="px-4 py-4">Kategori</th>
                                    <th class="px-4 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-sm italic">
                                @foreach($products as $p)
                                <tr class="hover:bg-stone-50/80 transition-all group">
                                    <td class="px-4 py-3 flex items-center gap-3">
                                        <div class="relative w-12 h-12">
                                            <img src="{{ asset('images/'.$p->image_url) }}" class="w-full h-full rounded-xl object-cover border shadow-sm group-hover:scale-105 transition-transform" onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-stone-800 not-italic">{{ $p->name }}</span>
                                            <span class="text-amber-700 font-black not-italic text-xs">Rp {{ number_format($p->price) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="bg-stone-100 px-3 py-1 rounded-full text-[9px] font-bold uppercase text-stone-400 group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors">{{ $p->category }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-2">
                                            <button onclick="editMode('{{ $p->id }}', '{{ $p->name }}', '{{ $p->price }}', '{{ $p->category }}')" 
                                                class="bg-blue-50 text-blue-600 w-9 h-9 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm flex items-center justify-center">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <a href="/admin/hapus/{{ $p->id }}" onclick="return confirm('Hapus menu {{ $p->name }}?')"
                                                class="bg-red-50 text-red-600 w-9 h-9 rounded-lg hover:bg-red-600 hover:text-white transition flex items-center justify-center shadow-sm">
                                                <i class="fas fa-trash text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function editMode(id, name, price, category) {
            // Ubah UI Form
            document.getElementById('form-title').innerText = "Edit Menu";
            document.getElementById('mode-badge').innerText = "EDIT MODE";
            document.getElementById('mode-badge').classList.replace('bg-stone-100', 'bg-blue-100');
            document.getElementById('mode-badge').classList.replace('text-stone-400', 'text-blue-600');
            
            document.getElementById('main-form').action = "/admin/update";
            document.getElementById('btn-submit').innerText = "Simpan Perubahan";
            document.getElementById('btn-submit').classList.replace('bg-stone-900', 'bg-blue-600');
            document.getElementById('btn-reset').classList.remove('hidden');
            
            // Isi Data
            document.getElementById('prod-id').value = id;
            document.getElementById('prod-name').value = name;
            document.getElementById('prod-price').value = price;
            document.getElementById('prod-cat').value = category;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('form-title').innerText = "Tambah Produk";
            document.getElementById('mode-badge').innerText = "CREATE MODE";
            document.getElementById('mode-badge').classList.replace('bg-blue-100', 'bg-stone-100');
            document.getElementById('mode-badge').classList.replace('text-blue-600', 'text-stone-400');
            
            document.getElementById('main-form').action = "/admin/simpan";
            document.getElementById('btn-submit').innerText = "Simpan Produk";
            document.getElementById('btn-submit').classList.replace('bg-blue-600', 'bg-stone-900');
            document.getElementById('btn-reset').classList.add('hidden');
            
            document.getElementById('main-form').reset();
        }
    </script>
</body>
</html>
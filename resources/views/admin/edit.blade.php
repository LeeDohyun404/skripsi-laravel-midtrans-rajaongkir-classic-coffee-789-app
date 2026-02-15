<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Edit Produk - Classic 789</title>
</head>
<body class="bg-[#f3f4f6]">
    <div class="flex justify-center items-center min-h-screen p-6">
        <div class="bg-white w-full max-w-xl rounded-[2rem] shadow-2xl overflow-hidden border border-amber-100/50">
            <div class="bg-stone-900 p-8 text-center text-white relative border-b border-amber-500/30">
                <a href="/admin" class="absolute left-6 top-1/2 -translate-y-1/2 text-stone-400 hover:text-white transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h2 class="font-serif italic text-3xl">Edit Menu</h2>
                <p class="text-[10px] text-amber-500 font-bold uppercase mt-1 tracking-[0.2em]">{{ $product->name }}</p>
            </div>

            <form action="/admin/update" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">

                <div>
                    <label class="text-[10px] font-black uppercase text-stone-400 block mb-2 tracking-widest">Nama Menu</label>
                    <input type="text" name="name" value="{{ $product->name }}" required 
                        class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:border-amber-700 transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase text-stone-400 block mb-2 tracking-widest">Harga (IDR)</label>
                        <input type="number" name="price" value="{{ $product->price }}" required 
                            class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:border-amber-700 transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-stone-400 block mb-2 tracking-widest">Kategori</label>
                        <select name="category" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none">
                            <option value="makanan" {{ $product->category == 'makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="minuman" {{ $product->category == 'minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="paket" {{ $product->category == 'paket' ? 'selected' : '' }}>Paket</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-stone-400 block mb-2 tracking-widest">Ganti Foto (Opsional)</label>
                    <div class="flex items-center gap-4 p-4 bg-stone-50 rounded-2xl border border-dashed border-stone-300">
                        <img src="{{ asset('images/'.$product->image_url) }}" class="w-14 h-14 rounded-xl object-cover">
                        <input type="file" name="image" class="text-xs text-stone-500">
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <a href="/admin" class="flex-1 bg-stone-100 text-stone-500 text-center font-bold py-4 rounded-2xl hover:bg-stone-200 transition uppercase text-xs tracking-widest">Batal</a>
                    <button type="submit" class="flex-[2] bg-stone-900 text-white font-bold py-4 rounded-2xl hover:bg-amber-700 transition shadow-lg uppercase text-xs tracking-widest">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
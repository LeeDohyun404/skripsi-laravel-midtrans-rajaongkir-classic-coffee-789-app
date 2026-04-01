<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#faf9f6]">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-stone-100">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold tracking-tighter text-stone-900">
                CLASSIC<span class="text-amber-700">789</span>
            </a>
            
           <ul class="hidden md:flex space-x-8 font-semibold text-[13px] uppercase tracking-[0.15em] text-stone-600">
    <li><a href="/" class="hover:text-amber-700 transition">Beranda</a></li>

    <li class="relative group">
        <a href="/produk" class="hover:text-amber-700 transition flex items-center">
            Produk <i class="fas fa-chevron-down ml-1 text-[10px]"></i>
        </a>
        
     <ul class="absolute left-0 mt-2 w-48 bg-white border border-stone-100 shadow-xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 py-2 normal-case tracking-normal">
    
    <li><a href="/produk" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Semua Produk</a></li>
    <li><a href="/produk/Makanan" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Makanan</a></li>
    <li><a href="/produk/Minuman" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Minuman</a></li>

</ul>
    </li>

    <li><a href="/keranjang" class="hover:text-amber-700 transition flex items-center">
        Keranjang 
        <span class="ml-1 bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-full text-[10px]">
            {{ session('cart') ? count(session('cart')) : 0 }}
        </span>
    </a></li>
    <li><a href="/about" class="hover:text-amber-700 transition">About</a></li>
    <li><a href="/service" class="hover:text-amber-700 transition">Service</a></li>
</ul>

           <div class="flex items-center gap-6">
            @guest
                <a href="/login" class="text-stone-600 hover:text-amber-600 font-bold transition">LOGIN</a>
                <a href="/register" class="bg-stone-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-stone-800 transition shadow-lg">DAFTAR</a>
            @else
                <div class="flex items-center gap-4">
                    <span class="text-stone-600 font-medium">Halo, <b class="text-stone-900">{{ Auth::user()->name }}</b></span>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm uppercase tracking-widest">
                            Logout
                        </button>
                    </form>
                </div>
            @endguest
        </div>
        </div>
    </nav>
@include('header')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Classic 789 - Authentic Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:italic,wght@0,700;1,700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#fcfaf8] text-stone-900">

    <header class="py-16 text-center">
        <p class="text-amber-600 font-bold uppercase tracking-[0.4em] text-[10px] mb-4">Curated Selection</p>
        <h2 class="text-5xl font-serif italic mb-4">Daftar Menu Kami</h2>
        <div class="w-20 h-1 bg-amber-600 mx-auto rounded-full"></div>
    </header>

    <main class="container mx-auto px-6 pb-24">
        
        @if(session('success'))
        <div class="max-w-md mx-auto bg-green-600 text-white p-4 rounded-2xl mb-10 shadow-lg flex items-center gap-3 animate-fade-in">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <a href="/keranjang" class="ml-auto text-xs underline font-bold">Lihat Keranjang</a>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($products as $p)
            <div class="group bg-white rounded-[2.5rem] p-4 shadow-sm hover:shadow-2xl transition-all duration-500 border border-stone-100 flex flex-col h-full">
                
                <div class="relative aspect-square overflow-hidden rounded-[2rem] mb-6">
                    <img src="{{ asset('images/'.$p->image_url) }}" 
                         alt="{{ $p->name }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                         onerror="this.src='https://placehold.co/400x400?text=Kopi+Enak'">
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-stone-500 shadow-sm">
                            {{ $p->category }}
                        </span>
                    </div>
                </div>

                <div class="px-2 flex-grow">
                    <h3 class="text-xl font-bold text-stone-800 mb-1 leading-tight group-hover:text-amber-700 transition-colors">
                        {{ $p->name }}
                    </h3>
                    <p class="text-amber-700 font-extrabold text-lg mb-6">
                        Rp {{ number_format($p->price) }}
                    </p>
                </div>

                <form action="/cart/add" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $p->id }}">
                    <input type="hidden" name="name" value="{{ $p->name }}">
                    <input type="hidden" name="price" value="{{ $p->price }}">
                    <input type="hidden" name="image" value="{{ $p->image_url }}">

                    @auth
                        <button type="submit" class="w-full bg-stone-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-3 hover:bg-amber-600 transition-all shadow-lg">
                            <i class="fas fa-cart-plus"></i> Tambah Pesanan
                        </button>
                    @else
                        <a href="/login" class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold text-xs text-center block uppercase tracking-widest hover:bg-red-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login Untuk Membeli
                        </a>
                    @endauth
                </form>
            </div>
            @endforeach
        </div>
    </main>

    <footer class="bg-stone-900 py-12 text-center text-stone-500">
        <p class="text-xs uppercase tracking-widest font-bold">© 2026 Classic Coffee 789. All Rights Reserved.</p>
    </footer>

</body>
</html>
@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-orange-50 to-stone-100 py-12 sm:py-16 lg:py-24">
    {{-- Decorative blobs --}}
    <div class="absolute -top-16 -right-16 w-64 h-64 bg-amber-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 -left-20 w-80 h-80 bg-orange-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col-reverse lg:flex-row items-center gap-10 lg:gap-16">

            {{-- Text --}}
            <div class="flex-1 text-center lg:text-left">
                <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                    🍴 Makanan & Minuman Terbaik
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-stone-900 leading-tight mb-4">
                    Nikmati Cita Rasa<br>
                    <span class="text-amber-700 font-serif italic">Autentik</span>
                </h1>
                <p class="text-stone-500 text-base sm:text-lg max-w-md mx-auto lg:mx-0 mb-8 leading-relaxed">
                    Pesan makanan & minuman premium langsung ke pintumu. Segar, lezat, dan siap dinikmati setiap hari.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <a href="/produk" class="bg-stone-900 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-amber-700 transition shadow-lg text-center">
                        Belanja Sekarang
                    </a>
                    <a href="/about" class="border-2 border-stone-300 text-stone-700 px-8 py-3.5 rounded-xl font-bold hover:border-amber-600 hover:text-amber-700 transition text-center">
                        Tentang Kami
                    </a>
                </div>
            </div>

            {{-- Hero Image Placeholder --}}
            <div class="flex-1 w-full max-w-sm sm:max-w-md lg:max-w-none">
                <div class="relative">
                    <div class="bg-gradient-to-br from-amber-200 to-orange-300 rounded-3xl aspect-square sm:aspect-[4/3] lg:aspect-square flex items-center justify-center shadow-2xl">
                        <div class="text-center">
                            <div class="text-7xl sm:text-8xl mb-3">🍱</div>
                            <p class="text-amber-800 font-bold text-sm">Foto Produk Unggulan</p>
                        </div>
                    </div>
                    {{-- Floating badge --}}
                    <div class="absolute -bottom-4 -left-4 bg-white shadow-xl rounded-2xl px-4 py-3 flex items-center gap-2 border border-amber-100">
                        <span class="text-2xl">⭐</span>
                        <div>
                            <p class="text-xs text-stone-500 font-medium">Rating Toko</p>
                            <p class="text-sm font-bold text-stone-900">4.9 / 5.0</p>
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 bg-amber-500 text-white shadow-xl rounded-2xl px-4 py-3 text-center">
                        <p class="text-xs font-bold">FREE ONGKIR</p>
                        <p class="text-[10px] opacity-80">Min. order 50rb</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     STATS / KEUNGGULAN
═══════════════════════════════════════════ --}}
<section class="bg-stone-900 text-white py-8 sm:py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            @foreach([
                ['🛍️', '10.000+', 'Pesanan'],
                ['😊', '5.000+', 'Pelanggan'],
                ['🍽️', '100+',   'Menu'],
                ['⚡', '< 1 Jam', 'Pengiriman'],
            ] as [$icon, $num, $label])
            <div>
                <div class="text-2xl mb-1">{{ $icon }}</div>
                <div class="text-2xl sm:text-3xl font-bold text-amber-400">{{ $num }}</div>
                <div class="text-stone-400 text-xs sm:text-sm font-medium">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     KATEGORI
═══════════════════════════════════════════ --}}
<section class="py-12 sm:py-16 bg-[#faf9f6]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 mb-2">Kategori Pilihan</h2>
            <p class="text-stone-500 text-sm sm:text-base">Temukan menu favoritmu</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6 max-w-2xl mx-auto">
            @foreach([
                ['🍔', 'Makanan',  '/produk/Makanan', 'from-orange-100 to-amber-100',  'text-orange-700'],
                ['☕', 'Minuman',  '/produk/Minuman', 'from-amber-100 to-yellow-100',   'text-amber-700'],
                ['🛒', 'Semua',    '/produk',         'from-stone-100 to-stone-200',    'text-stone-700'],
            ] as [$icon, $nama, $link, $gradient, $textColor])
            <a href="{{ $link }}" class="group bg-gradient-to-br {{ $gradient }} rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-white">
                <span class="text-4xl sm:text-5xl mb-3 group-hover:scale-110 transition-transform duration-300">{{ $icon }}</span>
                <span class="font-bold {{ $textColor }} text-sm sm:text-base">{{ $nama }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     PRODUK TERBARU
═══════════════════════════════════════════ --}}
<section class="py-12 sm:py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-8 sm:mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900">Menu Terbaru</h2>
                <p class="text-stone-500 text-sm sm:text-base mt-1">Selalu ada yang baru setiap minggu</p>
            </div>
            <a href="/produk" class="shrink-0 text-amber-700 font-bold text-sm hover:text-amber-900 transition flex items-center gap-1">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if(isset($latestProducts) && $latestProducts->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($latestProducts as $product)
            <div class="group bg-white rounded-2xl overflow-hidden border border-stone-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                {{-- Gambar --}}
                <div class="relative overflow-hidden bg-amber-50 aspect-square">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-5xl">🍽️</div>
                    @endif
                    @if($product->is_new ?? false)
                        <span class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">BARU</span>
                    @endif
                    <button class="absolute top-2 right-2 w-7 h-7 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-stone-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                        <i class="fas fa-heart text-xs"></i>
                    </button>
                </div>
                {{-- Info --}}
                <div class="p-3 sm:p-4">
                    <p class="text-[10px] sm:text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">{{ $product->category }}</p>
                    <h3 class="font-bold text-stone-900 text-sm sm:text-base leading-tight mb-2 line-clamp-2">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-amber-700 font-bold text-sm sm:text-base">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <a href="/produk/{{ $product->id }}"
                           class="bg-stone-900 text-white text-[10px] sm:text-xs font-bold px-2.5 sm:px-3 py-1.5 rounded-lg hover:bg-amber-700 transition whitespace-nowrap">
                            + Keranjang
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Placeholder kalau belum ada produk --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach(['Nasi Goreng Special', 'Es Kopi Susu', 'Mie Ayam Bakso', 'Jus Alpukat'] as $i => $nama)
            <div class="group bg-white rounded-2xl overflow-hidden border border-stone-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="bg-gradient-to-br from-amber-50 to-orange-100 aspect-square flex items-center justify-center text-5xl">
                    {{ ['🍳','☕','🍜','🥤'][$i] }}
                </div>
                <div class="p-3 sm:p-4">
                    <p class="text-[10px] text-amber-700 font-semibold uppercase tracking-wide mb-1">
                        {{ $i < 2 ? 'Makanan' : 'Minuman' }}
                    </p>
                    <h3 class="font-bold text-stone-900 text-sm leading-tight mb-2">{{ $nama }}</h3>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-amber-700 font-bold text-sm">Rp 25.000</span>
                        <button class="bg-stone-900 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg hover:bg-amber-700 transition">+ Keranjang</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════
     PROMO / BANNER
═══════════════════════════════════════════ --}}
<section class="py-10 sm:py-14 bg-[#faf9f6]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-stone-900 via-amber-900 to-stone-900 rounded-3xl overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between px-6 sm:px-10 lg:px-16 py-10 sm:py-12 gap-6 text-center md:text-left">
                <div>
                    <span class="inline-block bg-amber-500/20 text-amber-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-3">
                        🔥 Promo Spesial
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2">
                        Diskon hingga <span class="text-amber-400">50%</span>
                    </h2>
                    <p class="text-stone-300 text-sm sm:text-base max-w-sm">
                        Gunakan kode promo dan hemat lebih banyak untuk setiap pembelian!
                    </p>
                </div>
                <div class="shrink-0">
                    <div class="bg-amber-500/10 border-2 border-amber-500/30 rounded-2xl px-6 py-4 mb-4 text-center">
                        <p class="text-stone-400 text-xs mb-1 uppercase tracking-widest">Kode Promo</p>
                        <p class="text-amber-400 font-bold text-2xl tracking-[0.3em]">CLASSIC50</p>
                    </div>
                    <a href="/produk" class="block bg-amber-500 hover:bg-amber-400 text-stone-900 font-bold py-3 px-8 rounded-xl transition text-center shadow-lg">
                        Klaim Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     BIJI KOPI LOYALTY (untuk user login)
═══════════════════════════════════════════ --}}
@auth
<section class="py-10 sm:py-14 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6 sm:p-8 text-center">
            <div class="text-4xl mb-3">☕</div>
            <h3 class="text-xl sm:text-2xl font-bold text-stone-900 mb-2">Program Biji Kopi</h3>
            <p class="text-stone-500 text-sm mb-4">
                Kumpulkan 10 biji kopi, dapatkan <span class="font-bold text-amber-700">minuman gratis!</span>
            </p>
            @php $bp = Auth::user()->coffee_bean_progress ?? 0; @endphp
            <div class="flex justify-center gap-2 mb-3 flex-wrap">
                @for($i = 0; $i < 10; $i++)
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-lg
                    {{ $i < $bp ? 'bg-amber-700 shadow-md' : 'bg-stone-200' }}">
                    {{ $i < $bp ? '☕' : '○' }}
                </div>
                @endfor
            </div>
            <p class="text-xs text-stone-500">
                {{ $bp }}/10 — Tinggal <strong class="text-amber-700">{{ 10 - $bp }}x</strong> lagi!
            </p>
        </div>
    </div>
</section>
@endauth

{{-- ═══════════════════════════════════════════
     TESTIMONI
═══════════════════════════════════════════ --}}
<section class="py-12 sm:py-16 bg-[#faf9f6]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 mb-2">Kata Mereka</h2>
            <p class="text-stone-500 text-sm sm:text-base">Ribuan pelanggan sudah puas!</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach([
                ['Andi S.',    'Makanannya enak banget, pengiriman cepat! Pasti order lagi.',          '⭐⭐⭐⭐⭐'],
                ['Rini M.',    'Es kopi susunya nagih, persis kayak cafe mahal tapi harganya oke.',    '⭐⭐⭐⭐⭐'],
                ['Budi K.',    'Paket makan siangnya lengkap dan porsinya gede. Recommended!',         '⭐⭐⭐⭐☆'],
            ] as [$nama, $review, $rating])
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-100 shadow-sm hover:shadow-md transition">
                <p class="text-sm mb-1">{{ $rating }}</p>
                <p class="text-stone-700 text-sm sm:text-base leading-relaxed mb-4 italic">"{{ $review }}"</p>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">{{ substr($nama, 0, 1) }}</span>
                    </div>
                    <span class="text-sm font-bold text-stone-800">{{ $nama }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     CTA BOTTOM
═══════════════════════════════════════════ --}}
<section class="py-12 sm:py-16 bg-amber-700">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3">
            Lapar? Pesan Sekarang! 🍽️
        </h2>
        <p class="text-amber-200 text-sm sm:text-base mb-6 max-w-md mx-auto">
            Lebih dari 100 menu tersedia, siap diantar ke lokasi kamu.
        </p>
        <a href="/produk" class="inline-block bg-white text-amber-700 font-bold px-10 py-4 rounded-xl hover:bg-amber-50 transition shadow-xl text-base">
            Lihat Semua Menu
        </a>
    </div>
</section>

@endsection
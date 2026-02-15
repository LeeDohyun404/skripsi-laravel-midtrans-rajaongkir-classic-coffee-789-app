@include('header')

<section class="relative h-[80vh] min-h-[500px] w-full bg-stone-900 overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/coffee.jpg') }}" class="w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-900/80 to-transparent"></div>
    </div>
    
    <div class="container mx-auto px-6 h-full flex items-center relative z-10">
        <div class="max-w-2xl">
            <span class="text-amber-500 font-bold tracking-[0.3em] uppercase text-xs mb-4 block">Premium Coffee Experience</span>
            <h1 class="text-white font-serif text-5xl md:text-7xl mb-6 italic">Awali harimu dengan rasa otentik.</h1>
            <p class="text-stone-300 text-lg mb-8 leading-relaxed font-light">
                Classic Coffee 789 menyajikan biji kopi pilihan dari petani lokal dengan teknik roasting manual yang menjaga aroma tetap sempurna.
            </p>
            <div class="flex space-x-4">
                <a href="/produk" class="bg-amber-600 text-white px-8 py-4 rounded-full font-bold text-sm hover:bg-amber-700 transition">PESAN SEKARANG</a>
                <a href="/about" class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-4 rounded-full font-bold text-sm hover:bg-white/20 transition">CERITA KAMI</a>
            </div>
        </div>
    </div>
</section>

<section class="py-24 container mx-auto px-6">
    <div class="flex justify-between items-end mb-12">
        <div>
            <h2 class="text-3xl font-serif italic text-stone-800">Menu Terfavorit</h2>
            <div class="w-12 h-1 bg-amber-600 mt-2"></div>
        </div>
        <a href="/produk" class="text-amber-700 font-bold text-sm hover:underline">Lihat Semua Menu &rarr;</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
        {{-- Looping Produk --}}
        @foreach($products as $product)
        <div class="group bg-white rounded-[2rem] p-4 shadow-sm hover:shadow-2xl transition-all duration-500 border border-stone-100">
            <div class="relative overflow-hidden rounded-[1.5rem] aspect-square mb-6">
                <img src="{{ asset('images/' . $product->image_url) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                @if($product->discount_percentage > 0)
                <div class="absolute top-4 left-4 bg-red-600 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase">
                    Save {{ $product->discount_percentage }}%
                </div>
                @endif
            </div>
            
            <div class="px-2">
                <h3 class="text-xl font-serif italic text-stone-800 mb-1">{{ $product->name }}</h3>
                <div class="flex items-center mb-4">
                    <i class="fas fa-star text-amber-400 text-xs"></i>
                    <span class="text-stone-400 text-[11px] ml-1 font-bold">{{ $product->average_rating }} ({{ $product->review_count }} reviews)</span>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <span class="text-2xl font-bold text-stone-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <form action="/cart/add" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="name" value="{{ $product->name }}">
                        <input type="hidden" name="price" value="{{ $product->price }}">
                        <input type="hidden" name="image" value="{{ $product->image_url }}">
                        
                        <button type="submit" class="bg-stone-900 text-white h-12 w-12 rounded-2xl hover:bg-amber-700 transition flex items-center justify-center shadow-lg shadow-stone-200">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

@include('footer')
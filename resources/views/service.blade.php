@include('header')

<div class="bg-[#faf9f6] min-h-screen pb-20">
    <div class="bg-stone-900 py-20 mb-16">
        <div class="container mx-auto px-6 text-center">
            <span class="text-amber-500 font-bold tracking-[0.3em] uppercase text-xs mb-3 block">What We Do</span>
            <h1 class="text-white font-serif text-5xl italic">Layanan Kami</h1>
        </div>
    </div>

    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-10 rounded-3xl border border-stone-100 shadow-sm text-center hover:shadow-xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-amber-700 transition">
                    <i class="fas fa-coffee text-amber-700 group-hover:text-white text-2xl"></i>
                </div>
                <h3 class="font-serif italic text-2xl mb-4 text-stone-800">Premium Coffee</h3>
                <p class="text-stone-500 text-sm leading-relaxed">Biji kopi pilihan yang diproses secara tradisional untuk menjaga aroma dan rasa yang kuat.</p>
            </div>

            <div class="bg-white p-10 rounded-3xl border border-stone-100 shadow-sm text-center hover:shadow-xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-amber-700 transition">
                    <i class="fas fa-utensils text-amber-700 group-hover:text-white text-2xl"></i>
                </div>
                <h3 class="font-serif italic text-2xl mb-4 text-stone-800">Dine In & Chill</h3>
                <p class="text-stone-500 text-sm leading-relaxed">Tempat yang nyaman untuk bekerja, bersantai, atau sekadar berkumpul bersama teman.</p>
            </div>

            <div class="bg-white p-10 rounded-3xl border border-stone-100 shadow-sm text-center hover:shadow-xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-amber-700 transition">
                    <i class="fas fa-truck text-amber-700 group-hover:text-white text-2xl"></i>
                </div>
                <h3 class="font-serif italic text-2xl mb-4 text-stone-800">Fast Delivery</h3>
                <p class="text-stone-500 text-sm leading-relaxed">Layanan pengantaran cepat untuk memastikan paket makanan Anda sampai dengan hangat.</p>
            </div>
        </div>
    </div>
</div>
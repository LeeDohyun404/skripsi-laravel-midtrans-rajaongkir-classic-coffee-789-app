@include('header')

<div class="bg-[#faf9f6] min-h-screen pb-20">
    <div class="bg-stone-900 py-20 mb-16">
        <div class="container mx-auto px-6 text-center">
            <span class="text-amber-500 font-bold tracking-[0.3em] uppercase text-xs mb-3 block">Our Story</span>
            <h1 class="text-white font-serif text-5xl italic">Tentang Classic 789</h1>
        </div>
    </div>

    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-stone-100 flex flex-col md:flex-row gap-10 items-center">
            <div class="w-full md:w-1/2">
                <img src="{{ asset('images/about-cafe.jpg') }}" class="rounded-2xl shadow-lg w-full h-80 object-cover" onerror="this.src='https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1000&auto=format&fit=crop'">
            </div>
            <div class="w-full md:w-1/2">
                <h2 class="text-3xl font-serif italic text-stone-800 mb-6">Sejak 2024</h2>
                <p class="text-stone-600 leading-relaxed mb-4">
                    Classic 789 lahir dari kecintaan kami terhadap cita rasa kopi autentik dan kehangatan dalam setiap cangkir. Kami percaya bahwa setiap seduhan memiliki cerita tersendiri.
                </p>
                <p class="text-stone-600 leading-relaxed">
                    Berawal dari kedai kecil, kini kami hadir untuk menemani hari-hari Anda dengan pilihan menu terbaik, mulai dari kopi pilihan hingga hidangan paket hemat yang ramah di kantong.
                </p>
            </div>
        </div>
    </div>
</div>
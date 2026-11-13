@include('header')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Paket Menu - Classic Coffee 789</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Playfair+Display:italic,wght@0,700;1,700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(180, 83, 9, 0.15);
        }
    </style>
</head>
<body class="bg-[#fcfaf8] text-stone-900 min-h-screen flex flex-col justify-between">

    <div>
        {{-- Header Section --}}
        <header class="pt-16 pb-12 text-center px-6">
            <span class="inline-block bg-amber-100 text-amber-800 text-[11px] font-bold uppercase tracking-[0.3em] px-4 py-1.5 rounded-full mb-4 shadow-sm">
                <i class="fas fa-boxes-stacked mr-1.5"></i> Special Bundle Offers
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif italic text-stone-900 mb-4">
                Pilih Kategori Paket
            </h1>
            <p class="text-stone-500 max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Nikmati perpaduan racikan kopi otentik dan kesegaran thai tea khas Classic 789 dalam paket hemat istimewa.
            </p>
            <div class="w-24 h-1 bg-amber-600 mx-auto rounded-full mt-6"></div>
        </header>

        {{-- Main Content: Selection Cards --}}
        <main class="container mx-auto px-6 max-w-5xl pb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- CARD 1: PAKET KOPI --}}
                <div class="card-hover bg-white rounded-[2.5rem] border border-amber-100 overflow-hidden shadow-sm flex flex-col justify-between p-8 relative group">
                    <div class="absolute -right-8 -top-8 w-36 h-36 bg-amber-50 rounded-full blur-2xl group-hover:bg-amber-100 transition-all duration-500"></div>

                    <div>
                        {{-- Top Tag & Icon --}}
                        <div class="flex items-center justify-between mb-8">
                            <span class="bg-amber-600 text-white text-[11px] font-bold px-3.5 py-1.5 rounded-full uppercase tracking-widest shadow-sm">
                                <i class="fas fa-fire mr-1 text-amber-200"></i> Favorit Pelanggan
                            </span>
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 text-2xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-mug-hot"></i>
                            </div>
                        </div>

                        {{-- Title & Desc --}}
                        <h2 class="text-3xl font-serif italic text-stone-900 mb-3 group-hover:text-amber-700 transition-colors">
                            Paket Kopi
                        </h2>
                        <p class="text-stone-500 text-sm leading-relaxed mb-6">
                            Racikan biji kopi pilihan dengan kombinasi susu gurih dan aroma harum khas. Tersedia varian Caramel Latte, Hazelnut Latte, Sakura Latte, hingga Lyvhee Coffee.
                        </p>

                        {{-- Features List --}}
                        <ul class="space-y-3 mb-8 text-sm text-stone-600">
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>100% Biji Kopi Asli Berkualitas</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Rasa Seimbang (Aroma Kuat & Creamy)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Harga Lebih Hemat Dibandingkan Satuan</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Action Button --}}
                    <div>
                        <a href="/produk/Paket/Kopi"
                           class="w-full bg-stone-900 text-white py-4 rounded-2xl font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-3 group-hover:bg-amber-700 transition-all shadow-md group-hover:shadow-amber-700/20">
                            <span>Lihat Paket Kopi</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                {{-- CARD 2: PAKET THAI TEA --}}
                <div class="card-hover bg-white rounded-[2.5rem] border border-orange-100 overflow-hidden shadow-sm flex flex-col justify-between p-8 relative group">
                    <div class="absolute -right-8 -top-8 w-36 h-36 bg-orange-50 rounded-full blur-2xl group-hover:bg-orange-100 transition-all duration-500"></div>

                    <div>
                        {{-- Top Tag & Icon --}}
                        <div class="flex items-center justify-between mb-8">
                            <span class="bg-orange-600 text-white text-[11px] font-bold px-3.5 py-1.5 rounded-full uppercase tracking-widest shadow-sm">
                                <i class="fas fa-star mr-1 text-orange-200"></i> Best Seller Segar
                            </span>
                            <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 text-2xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-glass-water"></i>
                            </div>
                        </div>

                        {{-- Title & Desc --}}
                        <h2 class="text-3xl font-serif italic text-stone-900 mb-3 group-hover:text-orange-600 transition-colors">
                            Paket Thai Tea
                        </h2>
                        <p class="text-stone-500 text-sm leading-relaxed mb-6">
                            Daun teh asli Thailand pilihan dengan perpaduan susu kental manis dan krimer gurih yang creamy. Sangat segar dinikmati dingin saat cuaca terik.
                        </p>

                        {{-- Features List --}}
                        <ul class="space-y-3 mb-8 text-sm text-stone-600">
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Daun Teh Asli Thailand Pilihan</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Manis Gurih Pas & Sangat Creamy</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Porsi Puas Cocok untuk Rame-Rame</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Action Button --}}
                    <div>
                        <a href="/produk/Paket/Thai-Tea"
                           class="w-full bg-stone-900 text-white py-4 rounded-2xl font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-3 group-hover:bg-orange-600 transition-all shadow-md group-hover:shadow-orange-600/20">
                            <span>Lihat Paket Thai Tea</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Bottom Banner / Info --}}
            <div class="mt-12 bg-gradient-to-r from-amber-900 to-stone-900 rounded-3xl p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
                <div class="flex items-center gap-4 text-center md:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-400 text-2xl shrink-0">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-amber-200">Ingin Memesan Menu Satuan?</h4>
                        <p class="text-stone-300 text-xs sm:text-sm mt-0.5">Lihat seluruh katalog minuman kopi, non-kopi, dan camilan makanan kami.</p>
                    </div>
                </div>
                <a href="/produk"
                   class="bg-white text-stone-900 hover:bg-amber-100 px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition shrink-0 shadow">
                    Semua Produk
                </a>
            </div>
        </main>
    </div>

    @include('footer')

</body>
</html>


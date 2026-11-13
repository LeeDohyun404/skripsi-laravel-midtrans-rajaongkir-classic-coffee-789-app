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

        /* ── Biji Kopi ── */
        .bean { width: 14px; height: 20px; display: inline-block; }
        .bean svg { width: 100%; height: 100%; }
        .bean.filled svg .b { fill: #78350f; }
        .bean.filled svg .l { stroke: #f59e0b; }
        .bean.empty  svg .b { fill: #d6d3d1; }
        .bean.empty  svg .l { stroke: #a8a29e; }

        /* ── Bar container ── */
        .bean-bar {
            display: flex;
            align-items: center;
            gap: 2px;
            background: #fef9f0;
            border: 1px solid #fde68a;
            border-radius: 99px;
            padding: 4px 10px;
        }
        .bean-bar .lbl {
            font-size: 10px;
            font-weight: 800;
            color: #92400e;
            margin-left: 4px;
            white-space: nowrap;
        }

        /* ── Tooltip ── */
        .bean-wrap { position: relative; }
        .bean-tip {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #1c1917;
            color: #fef3c7;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 11px;
            border-radius: 8px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .2s;
            z-index: 99;
        }
        .bean-tip::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-bottom-color: #1c1917;
        }
        .bean-wrap:hover .bean-tip { opacity: 1; }

        /* ── Voucher glow ── */
        @keyframes vGlow {
            0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,.5); }
            50%      { box-shadow: 0 0 0 5px rgba(245,158,11,0); }
        }
        .voucher-glow { animation: vGlow 2s infinite; }

        /* ── Mobile menu slide ── */
        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }
        #mobile-menu.open {
            max-height: 600px;
        }
    </style>
</head>
<body class="bg-[#faf9f6]">

<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-stone-100 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">

        {{-- Logo --}}
        <a href="/" class="text-xl sm:text-2xl font-bold tracking-tighter text-stone-900">
            CLASSIC<span class="text-amber-700">789</span>
        </a>

        {{-- Desktop Nav --}}
        <ul class="hidden md:flex space-x-8 font-semibold text-[13px] uppercase tracking-[0.15em] text-stone-600">
            <li><a href="/" class="hover:text-amber-700 transition">Beranda</a></li>

            <li class="relative group">
                <a href="/produk" class="hover:text-amber-700 transition flex items-center">
                    Produk <i class="fas fa-chevron-down ml-1 text-[10px]"></i>
                </a>
                <ul class="absolute left-0 mt-2 w-48 bg-white border border-stone-100 shadow-xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 py-2 normal-case tracking-normal">
                    <li><a href="/produk"         class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Semua Produk</a></li>
                    <li><a href="/produk/Makanan" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Makanan</a></li>
                    <li><a href="/produk/Minuman" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Minuman</a></li>
                    <li>
                        <a href="/paket" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition flex items-center justify-between font-semibold text-amber-700">
                            <span>Paket</span>
                            <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Hemat</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="/keranjang" class="hover:text-amber-700 transition flex items-center">
                    Keranjang
                    <span class="ml-1 bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-full text-[10px]">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
            </li>
            <li><a href="/about"   class="hover:text-amber-700 transition">About</a></li>
            <li><a href="/service" class="hover:text-amber-700 transition">Service</a></li>
        </ul>

        {{-- Desktop Right --}}
        <div class="hidden md:flex items-center gap-4">
            @guest
                <a href="/login"    class="text-stone-600 hover:text-amber-600 font-bold transition">LOGIN</a>
                <a href="/register" class="bg-stone-900 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-stone-800 transition shadow-lg">DAFTAR</a>
            @else
                @php
                    $bp         = Auth::user()->coffee_bean_progress ?? 0;
                    $hasVoucher = isset($activeVoucher) && $activeVoucher;
                @endphp
                <div class="flex items-center gap-3">
                    @if($hasVoucher)
                        <a href="{{ route('profile') }}" class="bean-bar voucher-glow border-amber-400" style="text-decoration:none">
                            @for($i = 0; $i < 10; $i++)
                                <span class="bean filled">
                                    <svg viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse class="b" cx="8" cy="11" rx="7" ry="10"/>
                                        <path class="l" d="M8 2 Q11 7 11 11 Q11 15 8 20" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                                    </svg>
                                </span>
                            @endfor
                            <span class="lbl">🎉 Voucher!</span>
                        </a>
                    @else
                        <div class="bean-wrap">
                            <div class="bean-bar">
                                @for($i = 0; $i < 10; $i++)
                                    <span class="bean {{ $i < $bp ? 'filled' : 'empty' }}">
                                        <svg viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <ellipse class="b" cx="8" cy="11" rx="7" ry="10"/>
                                            <path class="l" d="M8 2 Q11 7 11 11 Q11 15 8 20" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                                        </svg>
                                    </span>
                                @endfor
                                <span class="lbl">{{ $bp }}/10</span>
                            </div>
                            <div class="bean-tip">{{ 10 - $bp }}x minuman lagi → minuman gratis!</div>
                        </div>
                    @endif

                    <a href="{{ route('profile') }}" class="flex items-center gap-2 hover:text-amber-600 transition text-stone-600">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="w-8 h-8 rounded-full object-cover border-2 border-amber-200"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display:none;" class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <span class="text-xs font-bold hidden lg:block text-stone-700">{{ Auth::user()->name }}</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm uppercase tracking-widest transition">Logout</button>
                    </form>
                </div>
            @endguest
        </div>

        {{-- Mobile: Keranjang + Hamburger --}}
        <div class="flex md:hidden items-center gap-3">
            <a href="/keranjang" class="relative text-stone-700 hover:text-amber-700 transition">
                <i class="fas fa-shopping-bag text-xl"></i>
                <span class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                    {{ session('cart') ? count(session('cart')) : 0 }}
                </span>
            </a>
            <button id="hamburger" class="text-stone-700 hover:text-amber-700 transition focus:outline-none" aria-label="Toggle menu">
                <i id="hamburger-icon" class="fas fa-bars text-2xl"></i>
            </button>
        </div>

    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="md:hidden bg-white border-t border-stone-100">
        <div class="px-4 pb-4 pt-2 space-y-1">

            <a href="/" class="block px-3 py-2.5 rounded-xl font-semibold text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition">
                <i class="fas fa-home mr-2 text-amber-600 w-5"></i> Beranda
            </a>

            {{-- Accordion Produk --}}
            <div x-data="{ open: false }">
                <button onclick="toggleProduk()" class="w-full flex justify-between items-center px-3 py-2.5 rounded-xl font-semibold text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition">
                    <span><i class="fas fa-box mr-2 text-amber-600 w-5"></i> Produk</span>
                    <i id="produk-arrow" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
                <div id="produk-sub" class="hidden pl-10 space-y-1 mt-1">
                    <a href="/produk"         class="block px-3 py-2 rounded-lg text-stone-600 hover:text-amber-700 hover:bg-amber-50 transition text-sm">Semua Produk</a>
                    <a href="/produk/Makanan" class="block px-3 py-2 rounded-lg text-stone-600 hover:text-amber-700 hover:bg-amber-50 transition text-sm">Makanan</a>
                    <a href="/produk/Minuman" class="block px-3 py-2 rounded-lg text-stone-600 hover:text-amber-700 hover:bg-amber-50 transition text-sm">Minuman</a>
                    <a href="/paket"          class="block px-3 py-2 rounded-lg text-amber-700 font-semibold hover:bg-amber-50 transition text-sm flex items-center justify-between">
                        <span>Paket</span>
                        <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Hemat</span>
                    </a>
                </div>
            </div>

            <a href="/about" class="block px-3 py-2.5 rounded-xl font-semibold text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition">
                <i class="fas fa-info-circle mr-2 text-amber-600 w-5"></i> About
            </a>
            <a href="/service" class="block px-3 py-2.5 rounded-xl font-semibold text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition">
                <i class="fas fa-concierge-bell mr-2 text-amber-600 w-5"></i> Service
            </a>

            <div class="border-t border-stone-100 pt-3 mt-2">
                @guest
                    <div class="flex gap-2">
                        <a href="/login"    class="flex-1 text-center py-2.5 border-2 border-stone-900 rounded-xl font-bold text-stone-800 hover:bg-stone-50 transition">LOGIN</a>
                        <a href="/register" class="flex-1 text-center py-2.5 bg-stone-900 rounded-xl font-bold text-white hover:bg-stone-700 transition">DAFTAR</a>
                    </div>
                @else
                    @php
                        $bp         = Auth::user()->coffee_bean_progress ?? 0;
                        $hasVoucher = isset($activeVoucher) && $activeVoucher;
                    @endphp
                    <div class="flex items-center justify-between px-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-2">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="w-9 h-9 rounded-full object-cover border-2 border-amber-200"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display:none;" class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-stone-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-stone-500">Biji kopi: {{ $bp }}/10</p>
                            </div>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-widest">Logout</button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

<script>
    // Hamburger toggle
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobile-menu');
    const hamIcon     = document.getElementById('hamburger-icon');

    hamburger.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
        hamIcon.classList.toggle('fa-bars');
        hamIcon.classList.toggle('fa-times');
    });

    // Produk sub-menu accordion
    function toggleProduk() {
        const sub   = document.getElementById('produk-sub');
        const arrow = document.getElementById('produk-arrow');
        sub.classList.toggle('hidden');
        arrow.style.transform = sub.classList.contains('hidden') ? '' : 'rotate(180deg)';
    }
</script>
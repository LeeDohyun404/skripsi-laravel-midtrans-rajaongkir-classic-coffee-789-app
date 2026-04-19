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
        .bean {
            width: 14px;
            height: 20px;
            display: inline-block;
        }
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

        /* ── Tooltip (muncul ke BAWAH) ── */
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
            top: auto;
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
                    <li><a href="/produk"         class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Semua Produk</a></li>
                    <li><a href="/produk/Makanan" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Makanan</a></li>
                    <li><a href="/produk/Minuman" class="block px-4 py-2 hover:bg-amber-50 hover:text-amber-700 transition">Minuman</a></li>
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

        <div class="flex items-center gap-4">
            @guest
                <a href="/login"    class="text-stone-600 hover:text-amber-600 font-bold transition">LOGIN</a>
                <a href="/register" class="bg-stone-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-stone-800 transition shadow-lg">DAFTAR</a>
            @else
                @php
                    $bp         = Auth::user()->coffee_bean_progress ?? 0;
                    $hasVoucher = isset($activeVoucher) && $activeVoucher;
                @endphp

                <div class="flex items-center gap-3">

                    {{-- ── BIJI KOPI PROGRESS ── --}}
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
                            <div class="bean-tip">
                                {{ 10 - $bp }}x minuman lagi → minuman gratis!
                            </div>
                        </div>
                    @endif

                    {{-- Avatar + Nama --}}
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 hover:text-amber-600 transition text-stone-600">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="w-8 h-8 rounded-full object-cover border-2 border-amber-200">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <span class="text-xs font-bold hidden sm:block text-stone-700">{{ Auth::user()->name }}</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm uppercase tracking-widest transition">
                            Logout
                        </button>
                    </form>
                </div>
            @endguest
        </div>

    </div>
</nav>
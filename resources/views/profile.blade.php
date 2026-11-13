@include('header')

<style>
    .tab-btn        { transition: all .25s; }
    .tab-btn.active { background: #292524; color: #fff; }
    .status-success   { background:#d1fae5; color:#065f46; }
    .status-pending   { background:#fef3c7; color:#92400e; }
    .status-cancelled { background:#fee2e2; color:#991b1b; }
    @media print {
        .no-print { display:none !important; }
        body { background:#fff; }
    }

    /* ── Voucher Card ── */
    .voucher-card {
        background: linear-gradient(135deg,#1c1917,#3b2a22);
        border-radius: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .voucher-card::before {
        content:'';
        position:absolute; top:-50px; right:-50px;
        width:180px; height:180px;
        background:rgba(251,191,36,.07);
        border-radius:50%;
    }
    .notch-l,.notch-r {
        position:absolute; width:18px; height:18px;
        background:#faf9f6; border-radius:50%; top:50%; transform:translateY(-50%);
    }
    .notch-l { left:-9px; }
    .notch-r { right:-9px; }
    .exp-badge {
        font-size:11px; font-weight:700;
        background:rgba(251,191,36,.15);
        border:1px solid rgba(251,191,36,.3);
        color:#fbbf24;
        padding:3px 10px; border-radius:99px;
        display:inline-flex; align-items:center; gap:5px;
    }
    .exp-badge.soon {
        background:rgba(239,68,68,.15);
        border-color:rgba(239,68,68,.4);
        color:#f87171;
    }
    @keyframes aGlow {
        0%,100% { box-shadow:0 0 0 0 rgba(251,191,36,.4); }
        50%      { box-shadow:0 0 18px 4px rgba(251,191,36,.15); }
    }
    .active-glow { animation:aGlow 2s infinite; }

    /* ── Bean row di tab voucher ── */
    .bean-lg { width:22px; height:30px; display:inline-block; }
    .bean-lg svg { width:100%; height:100%; }
</style>

<div class="min-h-screen bg-[#faf9f6] py-10 no-print">
<div class="container mx-auto px-4 max-w-4xl">

    {{-- ══ HEADER PROFIL ══ --}}
    <div class="bg-white rounded-3xl shadow-sm border border-stone-100 p-7 mb-5 flex flex-col md:flex-row items-center md:items-start gap-6">

        {{-- Foto --}}
        <div class="relative group shrink-0">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-amber-100 shadow relative bg-stone-100">
                @if(Auth::user()->avatar)
                    <img id="avatar-img-top" src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover"
                         onerror="this.style.display='none'; document.getElementById('avatar-fallback-top').style.display='flex';">
                    <div id="avatar-fallback-top" style="display:none;" class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-700 items-center justify-center">
                        <span class="text-white text-3xl font-bold font-serif">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</span>
                    </div>
                @else
                    <img id="avatar-img-top" src="" class="w-full h-full object-cover" style="display:none;">
                    <div id="avatar-fallback-top" class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                        <span class="text-white text-3xl font-bold font-serif">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</span>
                    </div>
                @endif
            </div>
            <label for="avatarInput" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                <i class="fas fa-camera text-white text-lg"></i>
            </label>
            <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*"
                       onchange="compressAndSubmitAvatar(this, 'avatarForm')">
            </form>
        </div>

        {{-- Info --}}
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-serif italic text-stone-900 mb-0.5">{{ Auth::user()->name }}</h1>
            <p class="text-stone-400 text-sm mb-3">{{ Auth::user()->email }}</p>
            <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                @if(Auth::user()->google_id)
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">
                        <i class="fab fa-google text-xs"></i> Google
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-stone-100 text-stone-600 text-xs font-bold px-3 py-1.5 rounded-full">
                        <i class="fas fa-envelope text-xs"></i> Email
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full">
                    <i class="fas fa-shopping-bag text-xs"></i> {{ $totalOrders }} Pesanan
                </span>
                @if($activeVoucher)
                    <span class="inline-flex items-center gap-1.5 bg-amber-600 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        <i class="fas fa-ticket-alt text-xs"></i> Voucher Tersedia
                    </span>
                @endif
            </div>
        </div>

        {{-- Statistik --}}
        <div class="flex gap-5 shrink-0 text-center">
            <div>
                <div class="text-2xl font-black text-stone-900">{{ $totalOrders }}</div>
                <div class="text-[10px] text-stone-400 uppercase tracking-widest">Total</div>
            </div>
            <div>
                <div class="text-2xl font-black text-green-600">{{ $successOrders }}</div>
                <div class="text-[10px] text-stone-400 uppercase tracking-widest">Sukses</div>
            </div>
            <div>
                <div class="text-2xl font-black text-amber-500">{{ $pendingOrders }}</div>
                <div class="text-[10px] text-stone-400 uppercase tracking-widest">Pending</div>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-3.5 mb-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-3.5 mb-4 flex items-center gap-3">
        <i class="fas fa-times-circle text-red-500"></i>
        <span class="font-semibold text-sm">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ══ TABS ══ --}}
    <div class="flex gap-2 mb-5 bg-stone-100 p-1.5 rounded-2xl w-fit flex-wrap">
        <button onclick="showTab('pesanan')" id="tab-pesanan"
            class="tab-btn active px-5 py-2 rounded-xl text-sm font-bold">
            <i class="fas fa-receipt mr-1.5"></i>Riwayat Pesanan
        </button>
        <button onclick="showTab('voucher')" id="tab-voucher"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-bold text-stone-600">
            <i class="fas fa-ticket-alt mr-1.5"></i>Voucher
            @if($activeVoucher)<span class="ml-1 w-2 h-2 bg-amber-500 rounded-full inline-block align-middle"></span>@endif
        </button>
        <button onclick="showTab('akun')" id="tab-akun"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-bold text-stone-600">
            <i class="fas fa-user-edit mr-1.5"></i>Kelola Akun
        </button>
        <button onclick="showTab('pengaduan')" id="tab-pengaduan"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-bold text-stone-600">
            <i class="fas fa-exclamation-circle mr-1.5"></i>Pengaduan
            @if(isset($totalPengaduan) && $totalPengaduan > 0)
                <span class="ml-1 w-2 h-2 bg-red-500 rounded-full inline-block align-middle"></span>
            @endif
        </button>
    </div>


    {{-- ══════════════════════════════════════
         TAB: RIWAYAT PESANAN
    ══════════════════════════════════════ --}}
    <div id="panel-pesanan">
        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl border border-stone-100 p-14 text-center shadow-sm">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-bag text-amber-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-serif italic text-stone-700 mb-2">Belum ada pesanan</h3>
                <p class="text-stone-400 text-sm mb-5">Yuk mulai pesan favoritmu!</p>
                <a href="/produk" class="bg-amber-600 text-white px-7 py-2.5 rounded-full font-bold text-sm hover:bg-amber-700 transition">Lihat Menu</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                @php
                    $isSuccess   = in_array($order->status, ['success','settlement','capture','completed','claimed']);
                    $isCancelled = in_array($order->status, ['cancelled','cancel','deny','expire']);
                    $isProcessing= $order->status === 'processing';
                    $isPending   = $order->status === 'pending';
                    $statusClass = $isSuccess ? 'status-success' : ($isCancelled ? 'status-cancelled' : ($isProcessing ? 'status-pending' : 'status-pending'));
                    $statusLabel = $isSuccess ? 'Sukses' : ($isCancelled ? 'Dibatalkan' : ($isProcessing ? 'Diproses' : 'Menunggu Pembayaran'));
                    $statusIcon  = $isSuccess ? 'fa-check-circle' : ($isCancelled ? 'fa-times-circle' : ($isProcessing ? 'fa-spinner fa-spin' : 'fa-clock'));
                @endphp

                <div class="bg-white rounded-3xl border border-stone-100 shadow-sm overflow-hidden">

                    {{-- Header order --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 pt-5 pb-4 border-b border-stone-50">
                        <div>
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="font-mono font-bold text-stone-800 text-sm">{{ $order->number }}</span>
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }} text-[9px]"></i> {{ $statusLabel }}
                                </span>
                                @if($order->used_voucher)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700">
                                        <i class="fas fa-ticket-alt text-[9px]"></i> Voucher
                                    </span>
                                @endif
                            </div>
                            <p class="text-stone-400 text-xs">
                                {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                            </p>
                        </div>

                        {{-- Tombol aksi --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-stone-400 text-xs font-bold uppercase tracking-wider">
                                {{ $order->order_type === 'take-away' ? '🛵 Take Away' : '🏠 Dine In' }}
                            </span>

                            <button onclick="toggleDetail('detail-{{ $order->id }}')"
                                class="bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1">
                                <i class="fas fa-chevron-down text-[10px]" id="icon-{{ $order->id }}"></i> Detail
                            </button>

                            @if($isPending)
                                @if($order->used_voucher)
                                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3.5 py-2 rounded-xl flex items-center gap-1">
                                        <i class="fas fa-money-bill-wave text-[10px]"></i> Bayar Tunai
                                    </span>
                                @elseif($order->snap_token)
                                    <button onclick="bayar('{{ $order->snap_token }}', {{ $order->id }})"
                                        id="btn-bayar-{{ $order->id }}"
                                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1">
                                        <i class="fas fa-credit-card text-[10px]"></i> Bayar
                                    </button>
                                @else
                                    <button onclick="getTokenDanBayar({{ $order->id }})"
                                        id="btn-bayar-{{ $order->id }}"
                                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1">
                                        <i class="fas fa-credit-card text-[10px]"></i> Bayar
                                    </button>
                                @endif
                            @elseif($isCancelled)
                                <span class="bg-red-100 text-red-400 text-xs font-bold px-3.5 py-2 rounded-xl flex items-center gap-1">
                                    <i class="fas fa-ban text-[10px]"></i> Dibatalkan
                                </span>
                            @elseif($isSuccess)
                                <a href="{{ route('orders.struk', $order->id) }}" target="_blank"
                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1">
                                    <i class="fas fa-print text-[10px]"></i> Struk
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Item preview --}}
                    <div class="px-6 py-3.5 flex flex-wrap gap-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-2">
                            @if($item->product_image)
                                <img src="{{ url('/foto-produk') . '?f=' . $item->product_image }}" class="w-8 h-8 rounded-xl object-cover border border-stone-100">
                            @else
                                <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                    <i class="fas fa-coffee text-amber-400 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-stone-700 font-medium">{{ $item->product_name }}
                                <span class="text-stone-400 text-xs">×{{ $item->quantity }}</span>
                            </span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Detail collapsible --}}
                    <div id="detail-{{ $order->id }}" class="hidden border-t border-stone-50">
                        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Rincian produk --}}
                            <div>
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-stone-400 mb-3">Rincian Produk</h4>
                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-stone-600">{{ $item->product_name }} <span class="text-stone-400 text-xs">×{{ $item->quantity }}</span></span>
                                        <span class="font-bold text-stone-800">Rp {{ number_format($item->subtotal,0,',','.') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="border-t border-dashed border-stone-200 mt-3 pt-3 space-y-1.5">
                                    <div class="flex justify-between text-sm text-stone-400">
                                        <span>Subtotal</span>
                                        <span>Rp {{ number_format($order->subtotal,0,',','.') }}</span>
                                    </div>
                                    @if($order->ongkir > 0)
                                    <div class="flex justify-between text-sm text-stone-400">
                                        <span>Ongkir ({{ $order->courier_name }} {{ $order->courier_service }})</span>
                                        <span>Rp {{ number_format($order->ongkir,0,',','.') }}</span>
                                    </div>
                                    @endif
                                    @if($order->used_voucher && $order->voucher_discount > 0)
                                    <div class="flex justify-between text-sm text-amber-600 font-semibold">
                                        <span><i class="fas fa-ticket-alt mr-1 text-xs"></i>Voucher Minuman Gratis</span>
                                        <span>- Rp {{ number_format($order->voucher_discount,0,',','.') }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between font-black text-stone-900 pt-1">
                                        <span>Total</span>
                                        <span>Rp {{ number_format($order->total_price,0,',','.') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info pesanan --}}
                            <div>
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-stone-400 mb-3">Info Pesanan</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">Jenis</span>
                                        <span class="font-semibold text-stone-700">{{ $order->order_type === 'take-away' ? 'Take Away' : 'Dine In' }}</span>
                                    </div>
                                    @if($order->order_type === 'take-away' && $order->courier_name)
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">Kurir</span>
                                        <span class="font-semibold text-stone-700">{{ $order->courier_name }} – {{ $order->courier_service }}</span>
                                    </div>
                                    @endif
                                    @if($order->address)
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">Alamat</span>
                                        <span class="font-semibold text-stone-700">{{ $order->address }}</span>
                                    </div>
                                    @endif
                                    @if($order->customer_phone)
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">No. HP</span>
                                        <span class="font-semibold text-stone-700">{{ $order->customer_phone }}</span>
                                    </div>
                                    @endif
                                    @if($order->notes)
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">Catatan</span>
                                        <span class="font-semibold text-stone-700">{{ $order->notes }}</span>
                                    </div>
                                    @endif
                                    <div class="flex gap-2">
                                        <span class="text-stone-400 w-24 shrink-0">Pembayaran</span>
                                        <span class="font-semibold text-stone-700">
                                            @if($order->used_voucher)
                                                <i class="fas fa-money-bill-wave text-amber-500 text-xs mr-1"></i>Tunai (Voucher)
                                            @else
                                                <i class="fas fa-credit-card text-amber-500 text-xs mr-1"></i>Midtrans
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Tracking Status (khusus Take Away) --}}
                    @if($order->order_type === 'take-away' && !in_array($order->status, ['pending']))
                    @php
                        $ts = $order->tracking_status ?? 'dibuat';
                        $steps = ['dibuat','dikemas','dikirim_ke_pengirim','dikirim','selesai'];
                        $stepLabels = [
                            'dibuat'              => ['label'=>'Pesanan Dibuat',         'icon'=>'📋'],
                            'dikemas'             => ['label'=>'Pesanan Dikemas',        'icon'=>'📦'],
                            'dikirim_ke_pengirim' => ['label'=>'Diserahkan ke Pengirim', 'icon'=>'🤝'],
                            'dikirim'             => ['label'=>'Sedang Dikirim',         'icon'=>'🚚'],
                            'selesai'             => ['label'=>'Pesanan Selesai',        'icon'=>'✅'],
                        ];
                        $currentStep = array_search($ts, $steps);
                        $isBatal = $ts === 'batal';
                    @endphp

                    <div class="border-t border-stone-100 px-6 py-5">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-stone-400 mb-4">🚚 Status Pengiriman</h4>

                        @if($isBatal)
                            <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 flex items-center gap-3">
                                <span class="text-lg">❌</span>
                                <div>
                                    <p class="text-sm font-bold text-red-700">Pengiriman Dibatalkan</p>
                                    @php $lastTracking = $order->trackings->last(); @endphp
                                    @if($lastTracking && $lastTracking->keterangan)
                                        <p class="text-xs text-red-500">{{ $lastTracking->keterangan }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Progress bar --}}
                            <div style="display:flex;align-items:flex-start;gap:0;margin-bottom:16px;">
                                @foreach($steps as $i => $step)
                                @php
                                    $done    = $currentStep !== false && $i <= $currentStep;
                                    $current = $currentStep !== false && $i === $currentStep;
                                    $cfg     = $stepLabels[$step];
                                @endphp
                                <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
                                    {{-- Connector line kiri --}}
                                    @if($i > 0)
                                    <div style="position:absolute;top:14px;right:50%;width:100%;height:2px;background:{{ $done ? '#d97706' : '#e5e7eb' }};z-index:0;"></div>
                                    @endif
                                    {{-- Icon --}}
                                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:1;position:relative;
                                        border:2px solid {{ $done ? '#d97706' : '#e5e7eb' }};
                                        background:{{ $current ? '#d97706' : ($done ? '#fef3c7' : '#f9fafb') }};">
                                        {{ $cfg['icon'] }}
                                    </div>
                                    {{-- Label --}}
                                    <p style="font-size:9px;text-align:center;margin-top:4px;font-weight:{{ $current ? '800' : '500' }};color:{{ $done ? '#92400e' : '#9ca3af' }};line-height:1.3;">
                                        {{ $cfg['label'] }}
                                    </p>
                                </div>
                                @endforeach
                            </div>

                            {{-- Tombol konfirmasi selesai --}}
                            @if($ts === 'dikirim')
                            <form action="{{ route('orders.selesai', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Konfirmasi pesanan sudah diterima?')">
                                @csrf
                                <button type="submit"
                                    style="width:100%;padding:10px;background:#16a34a;color:white;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;">
                                    ✅ Pesanan Sudah Diterima
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════
         TAB: VOUCHER & PROGRESS
    ══════════════════════════════════════ --}}
    <div id="panel-voucher" class="hidden space-y-5">

        {{-- Progress biji kopi --}}
        @php $bp = Auth::user()->coffee_bean_progress ?? 0; @endphp

        <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-serif italic text-stone-800 text-xl mb-0.5">Progress Biji Kopi</h3>
                    <p class="text-stone-400 text-xs">Setiap pembelian minuman = 1 biji kopi. Kumpulkan 10 untuk dapat minuman gratis!</p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black text-amber-700">{{ $bp }}</span>
                    <span class="text-lg font-bold text-stone-300">/10</span>
                </div>
            </div>

            {{-- Biji kopi besar --}}
            <div class="flex items-end gap-2 mb-4 flex-wrap">
                @for($i = 0; $i < 10; $i++)
                <div class="flex flex-col items-center gap-1">
                    <div class="bean-lg {{ $i < $bp ? 'opacity-100' : 'opacity-20' }} transition-all duration-300">
                        <svg viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="8" cy="11" rx="7" ry="10" fill="{{ $i < $bp ? '#78350f' : '#d6d3d1' }}"/>
                            <path d="M8 2 Q11 7 11 11 Q11 15 8 20" stroke="{{ $i < $bp ? '#f59e0b' : '#a8a29e' }}" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                        </svg>
                    </div>
                    <span class="text-[9px] font-bold {{ $i < $bp ? 'text-amber-600' : 'text-stone-300' }}">{{ $i+1 }}</span>
                </div>
                @endfor
            </div>

            {{-- Progress track --}}
            <div class="w-full bg-stone-100 rounded-full h-2 mb-2">
                <div class="bg-gradient-to-r from-amber-700 to-amber-400 h-2 rounded-full transition-all duration-700"
                     style="width:{{ ($bp/10)*100 }}%"></div>
            </div>
            <p class="text-xs text-stone-400">
                @if($bp >= 10)
                    🎉 Lengkap! Voucher minuman gratis sudah tersedia di bawah.
                @else
                    Butuh <strong class="text-amber-700">{{ 10 - $bp }} minuman lagi</strong> untuk dapat voucher gratis.
                @endif
            </p>
        </div>

        {{-- Voucher --}}
        <div>
            <h3 class="font-serif italic text-stone-700 text-lg mb-3">Voucher Saya</h3>

            @if($activeVoucher)
                @php
                    $exp      = \Carbon\Carbon::parse($activeVoucher->expires_at);
                    $daysLeft = now()->diffInDays($exp, false);
                    $hrsLeft  = now()->diffInHours($exp, false);
                    $soon     = $daysLeft <= 1;
                    $isUsing  = session('voucher_active', false);
                @endphp

                <div class="voucher-card {{ $isUsing ? 'active-glow' : '' }}">
                    <div class="notch-l"></div>
                    <div class="notch-r"></div>

                    <div class="flex relative z-10">
                        {{-- Kiri: info --}}
                        <div class="flex-1 p-6">
                            <p class="text-amber-400 text-[10px] font-black uppercase tracking-widest mb-2">
                                <i class="fas fa-ticket-alt mr-1"></i>Voucher Minuman Gratis
                            </p>
                            <h2 class="text-white text-xl font-black mb-1">1 Minuman Gratis</h2>
                            <p class="text-stone-400 text-xs mb-4 leading-relaxed">
                                1 item minuman (harga terendah di keranjang) digratiskan.<br>
                                Pembayaran <strong class="text-stone-300">tunai saja</strong>, tidak bisa via Midtrans.
                            </p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="exp-badge {{ $soon ? 'soon' : '' }}">
                                    <i class="fas fa-clock text-[9px]"></i>
                                    @if($hrsLeft <= 0) Kedaluwarsa!
                                    @elseif($daysLeft < 1) {{ $hrsLeft }} jam lagi
                                    @else {{ $daysLeft }} hari lagi
                                    @endif
                                </span>
                                <span class="text-stone-500 text-[11px]">
                                    s/d {{ $exp->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                </span>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="w-px bg-gradient-to-b from-transparent via-amber-900/40 to-transparent my-4"></div>

                        {{-- Kanan: tombol --}}
                        <div class="w-32 flex flex-col items-center justify-center gap-3 p-4">
                            <div class="text-center">
                                <div class="text-2xl mb-0.5">☕</div>
                                <div class="text-white text-[10px] font-black">CLASSIC<span class="text-amber-400">789</span></div>
                            </div>

                            @if($isUsing)
                                <form action="{{ route('voucher.deactivate') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs font-black px-4 py-2 rounded-xl transition">
                                        <i class="fas fa-times mr-1"></i>Batal
                                    </button>
                                </form>
                                <span class="text-green-400 text-[10px] font-bold text-center">✓ Aktif</span>
                            @else
                                <form action="{{ route('voucher.activate') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="voucher_id" value="{{ $activeVoucher->id }}">
                                    <button type="submit"
                                        class="bg-amber-500 hover:bg-amber-400 text-stone-900 text-xs font-black px-4 py-2 rounded-xl transition">
                                        <i class="fas fa-check mr-1"></i>Gunakan
                                    </button>
                                </form>
                                <span class="text-stone-500 text-[10px] text-center">Aktifkan saat belanja</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Syarat & Ketentuan --}}
                <div class="mt-3 bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4 text-xs text-amber-900 space-y-1">
                    <p class="font-bold mb-1">Syarat & Ketentuan</p>
                    <p>• Berlaku untuk <strong>1 item minuman</strong> dengan harga terendah di keranjang.</p>
                    <p>• Keranjang hanya boleh berisi <strong>minuman saja</strong> — tidak bisa digabung dengan makanan.</p>
                    <p>• Hanya untuk pesanan <strong>Makan di Sini (Dine-In)</strong> — tidak bisa diantar.</p>
                    <p>• Pembayaran hanya <strong>tunai (cash)</strong> — Midtrans tidak tersedia.</p>
                    <p>• Tidak bisa digabung dengan promo lain.</p>
                    <p>• Pembelian dengan voucher tidak menambah progress biji kopi.</p>
                </div>

            @else
                <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-10 text-center">
                    <div class="w-14 h-14 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ticket-alt text-stone-300 text-xl"></i>
                    </div>
                    <p class="text-stone-500 font-serif italic text-lg mb-1">Belum ada voucher</p>
                    <p class="text-stone-400 text-sm">Kamu sudah punya <strong class="text-amber-600">{{ $bp }}/10</strong> biji kopi.</p>
                    <a href="/produk/Minuman" class="inline-block mt-5 bg-amber-600 text-white px-6 py-2.5 rounded-full font-bold text-sm hover:bg-amber-700 transition">
                        Pesan Minuman
                    </a>
                </div>
            @endif

            {{-- Riwayat voucher terpakai --}}
            @if(isset($usedVouchers) && $usedVouchers->count() > 0)
            <div class="mt-4">
                <p class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Riwayat Terpakai</p>
                <div class="space-y-2">
                    @foreach($usedVouchers as $v)
                    <div class="bg-white border border-stone-100 rounded-2xl px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-stone-600">Voucher Minuman Gratis</p>
                            <p class="text-xs text-stone-400">{{ \Carbon\Carbon::parse($v->used_at)->locale('id')->isoFormat('D MMM YYYY') }}</p>
                        </div>
                        <span class="text-xs font-bold text-stone-400 bg-stone-100 px-3 py-1 rounded-full">Terpakai</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>


    {{-- ══════════════════════════════════════
         TAB: KELOLA AKUN
    ══════════════════════════════════════ --}}
    <div id="panel-akun" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Ubah Nama --}}
            <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-6">
                <h3 class="font-serif italic text-stone-800 text-xl mb-1">Ubah Nama</h3>
                <p class="text-stone-400 text-xs mb-5">Nama yang tampil di profil dan pesanan kamu.</p>
                <form action="{{ route('profile.name') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" required
                            class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition"
                            placeholder="Nama baru">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full bg-stone-900 text-white py-3 rounded-2xl font-bold text-sm hover:bg-amber-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </form>
            </div>

            {{-- Ubah Password --}}
            <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-6">
                <h3 class="font-serif italic text-stone-800 text-xl mb-1">Ubah Password</h3>
                <p class="text-stone-400 text-xs mb-5">Keamanan akun kamu.</p>

                @if(Auth::user()->google_id)
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3 items-start">
                    <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                        <i class="fab fa-google text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-bold text-blue-900 text-sm mb-1">Akun Google</p>
                        <p class="text-blue-600 text-xs leading-relaxed">Password dikelola oleh Google.</p>
                        <a href="https://myaccount.google.com/security" target="_blank"
                            class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 mt-2 transition">
                            Buka Google Account <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    </div>
                </div>
                @else
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Password Lama</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="pw_old" required placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition pr-12">
                            <button type="button" onclick="togglePw('pw_old','eye_old')" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                                <i class="fas fa-eye text-sm" id="eye_old"></i>
                            </button>
                        </div>
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="pw_new" required minlength="8" placeholder="Min. 8 karakter"
                                class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition pr-12">
                            <button type="button" onclick="togglePw('pw_new','eye_new')" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                                <i class="fas fa-eye text-sm" id="eye_new"></i>
                            </button>
                        </div>
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password baru"
                            class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition">
                    </div>
                    <button type="submit" class="w-full bg-stone-900 text-white py-3 rounded-2xl font-bold text-sm hover:bg-amber-700 transition">
                        <i class="fas fa-lock mr-2"></i>Ubah Password
                    </button>
                </form>
                @endif
            </div>

            {{-- Foto Profil --}}
            <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-6 md:col-span-2">
                <h3 class="font-serif italic text-stone-800 text-xl mb-1">Foto Profil</h3>
                <p class="text-stone-400 text-xs mb-5">Format JPG, PNG, atau GIF. Maks. 2MB.</p>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-stone-100 shadow-sm shrink-0 relative bg-stone-100">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" id="avatarPreview2" class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; document.getElementById('avatar-fallback-edit').style.display='flex';">
                            <div id="avatar-fallback-edit" style="display:none;" class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-700 items-center justify-center">
                                <span class="text-white text-2xl font-bold">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</span>
                            </div>
                        @else
                            <img src="" id="avatarPreview2" class="w-full h-full object-cover" style="display:none;">
                            <div id="avatar-fallback-edit" class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <form id="avatarForm2" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="flex-1">
                        @csrf
                        <div class="border-2 border-dashed border-stone-200 rounded-2xl p-5 text-center hover:border-amber-400 transition cursor-pointer relative">
                            <input type="file" id="avatarInput2" name="avatar" accept="image/*" onchange="compressAndSubmitAvatar(this, 'avatarForm2')"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            <i class="fas fa-cloud-upload-alt text-stone-300 text-2xl mb-2"></i>
                            <p class="text-stone-400 text-sm font-semibold" id="avatarFileName">Klik untuk upload foto</p>
                            <p class="text-stone-300 text-xs mt-1">JPG, PNG, GIF — maks 2MB</p>
                        </div>
                        @error('avatar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <button type="button" onclick="document.getElementById('avatarInput2').click()"
                            class="mt-3 w-full bg-stone-900 text-white py-3 rounded-2xl font-bold text-sm hover:bg-amber-700 transition">
                            <i class="fas fa-upload mr-2"></i>Upload Foto
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════
         TAB: PENGADUAN
    ══════════════════════════════════════ --}}
    <div id="panel-pengaduan" class="hidden">

        {{-- Daftar pengaduan sebelumnya --}}
        @if(isset($complaints) && $complaints->count() > 0)
        <div class="mb-6">
            <h3 class="text-stone-700 font-bold text-sm mb-3">📋 Riwayat Pengaduan</h3>
            <div class="space-y-3">
                @foreach($complaints as $c)
                @php
                    $jCfg = \App\Models\OrderComplaint::getJenis($c->jenis);
                    $sCfg = \App\Models\OrderComplaint::getStatus($c->status);
                @endphp
                <div class="bg-white rounded-2xl border border-stone-100 shadow-sm p-4">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">{{ $jCfg['icon'] }}</span>
                            <span class="font-bold text-sm text-stone-800">{{ $jCfg['label'] }}</span>
                        </div>
                        <span style="background:{{ $sCfg['bg'] }};color:{{ $sCfg['color'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;">
                            {{ $sCfg['label'] }}
                        </span>
                    </div>
                    <p class="text-xs text-stone-500 mb-1">Pesanan: <strong>{{ $c->order->number ?? '-' }}</strong> · {{ \Carbon\Carbon::parse($c->created_at)->locale('id')->isoFormat('D MMM YYYY') }}</p>
                    <p class="text-sm text-stone-600 mb-2">{{ $c->detail }}</p>
                    @if($c->foto)
                    <div class="mb-2">
                        <a href="{{ $c->foto }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-amber-700 hover:text-amber-800 font-semibold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                            📷 Lihat Foto Bukti
                        </a>
                    </div>
                    @endif
                    @if($c->catatan_operator)
                    <div class="mt-2 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2">
                        <p class="text-xs font-bold text-amber-700 mb-1">Respon Tim Classic Coffee:</p>
                        <p class="text-xs text-amber-800">{{ $c->catatan_operator }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Form buat pengaduan baru --}}
        <div class="bg-white rounded-3xl border border-stone-100 shadow-sm p-6">
            <h3 class="font-serif italic text-stone-800 text-xl mb-1">Buat Pengaduan Baru</h3>
            <p class="text-stone-400 text-xs mb-5">Laporkan masalah terkait pesanan kamu. Sertakan foto bukti untuk mempercepat proses.</p>

            <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Pilih Pesanan --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Pilih Pesanan *</label>
                    <select name="order_id" required
                        class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition">
                        <option value="">-- Pilih pesanan --</option>
                        @foreach($orders as $o)
                            @if($o->order_type === 'take-away' && $o->status === 'success')
                            <option value="{{ $o->id }}">{{ $o->number }} — {{ \Carbon\Carbon::parse($o->created_at)->locale('id')->isoFormat('D MMM YYYY') }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('order_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Jenis Pengaduan --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Jenis Pengaduan *</label>
                    <select name="jenis" required
                        class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition">
                        <option value="">-- Pilih jenis pengaduan --</option>
                        @foreach(\App\Models\OrderComplaint::$jenisConfig as $key => $val)
                            <option value="{{ $key }}">{{ $val['icon'] }} {{ $val['label'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Detail Pengaduan --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Detail Pengaduan *</label>
                    <textarea name="detail" rows="4" required
                        class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition resize-none"
                        placeholder="Jelaskan masalah yang kamu alami secara detail..."></textarea>
                    @error('detail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Foto Bukti --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-widest text-stone-400 mb-2">Foto Bukti *</label>
                    <input type="file" name="foto" accept="image/*" required
                        class="w-full border border-stone-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition">
                    <p class="text-xs text-stone-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    @error('foto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="w-full bg-stone-900 hover:bg-amber-700 text-white font-bold py-3 rounded-2xl text-sm transition">
                    📤 Kirim Pengaduan
                </button>
            </form>
        </div>
    </div>

</div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

<script>
function showTab(tab) {
    ['pesanan','voucher','akun','pengaduan'].forEach(t => {
        document.getElementById('panel-'+t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('tab-'+t);
        btn.classList.toggle('active', t === tab);
        btn.classList.toggle('text-stone-600', t !== tab);
    });
}

function toggleDetail(id) {
    const el   = document.getElementById(id);
    const ordId = id.replace('detail-','');
    const icon  = document.getElementById('icon-'+ordId);
    const hidden = el.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down', hidden);
    icon.classList.toggle('fa-chevron-up',  !hidden);
}

function togglePw(inp, ico) {
    const i = document.getElementById(inp);
    const c = document.getElementById(ico);
    i.type = i.type === 'text' ? 'password' : 'text';
    c.classList.toggle('fa-eye');
    c.classList.toggle('fa-eye-slash');
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        document.getElementById('avatarFileName').textContent = input.files[0].name;
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('avatarPreview2');
            if (p) {
                p.src = e.target.result;
                p.style.display = 'block';
                const fb = document.getElementById('avatar-fallback-edit');
                if (fb) fb.style.display = 'none';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Kompres gambar via Canvas (max 800px, quality 0.82) lalu kirim sebagai base64 JSON.
 * Ini mencegah error 2MB karena foto kamera asli bisa 5-10MB.
 */
function compressAndSubmitAvatar(input, formId) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const fname = document.getElementById('avatarFileName');
    if (fname) fname.textContent = 'Memproses foto...';

    const reader = new FileReader();
    reader.onload = function(e) {
        // Tampilkan preview instan ke user saat itu juga!
        const p2 = document.getElementById('avatarPreview2');
        if (p2) {
            p2.src = e.target.result;
            p2.style.display = 'block';
            const fb2 = document.getElementById('avatar-fallback-edit');
            if (fb2) fb2.style.display = 'none';
        }
        const topImg = document.getElementById('avatar-img-top');
        if (topImg) {
            topImg.src = e.target.result;
            topImg.style.display = 'block';
            const topFb = document.getElementById('avatar-fallback-top');
            if (topFb) topFb.style.display = 'none';
        }

        const img = new Image();
        img.onload = function() {
            const MAX = 800;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else       { w = Math.round(w * MAX / h); h = MAX; }
            }
            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
            const base64  = dataUrl.split(',')[1];

            // Kirim via fetch JSON agar tidak terkena limit upload file PHP
            const form = document.getElementById(formId);
            const csrf = form.querySelector('[name="_token"]').value;
            if (fname) fname.textContent = 'Mengunggah ke server...';

            fetch('{{ route("profile.avatar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ avatar_base64: base64 })
            })
            .then(r => r.json())
            .then(data => {
                if (data.url) {
                    if (fname) fname.textContent = 'Foto berhasil disimpan!';
                    // Tunggu sebentar lalu reload agar sinkron ke header
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    if (fname) fname.textContent = data.error || 'Gagal upload';
                    alert(data.error || 'Upload gagal, coba lagi.');
                }
            })
            .catch(() => {
                if (fname) fname.textContent = 'Klik untuk upload foto';
                alert('Koneksi gagal, coba lagi.');
            });
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function bayar(snapToken, orderId) {
    snap.pay(snapToken, {
        onSuccess: () => location.reload(),
        onPending: () => location.reload(),
        onError:   () => getTokenDanBayar(orderId),
        onClose:   () => {}
    });
}

function getTokenDanBayar(orderId) {
    const btn = document.getElementById('btn-bayar-' + orderId);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i> Memuat...'; }

    fetch('/orders/' + orderId + '/refresh-token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.snap_token) {
            snap.pay(data.snap_token, {
                onSuccess: () => location.reload(),
                onPending: () => location.reload(),
                onError:   () => location.reload(),
                onClose:   () => {
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-credit-card text-[10px]"></i> Bayar'; }
                }
            });
        } else {
            alert(data.error || 'Gagal memuat pembayaran.');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-credit-card text-[10px]"></i> Bayar'; }
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-credit-card text-[10px]"></i> Bayar'; }
    });
}

@if($errors->has('name') || $errors->has('avatar') || $errors->has('current_password') || $errors->has('password'))
    showTab('akun');
@endif
@if(session('voucher_earned'))
    showTab('voucher');
@endif
@if(session()->has('_old_input') && request()->routeIs('complaint.store'))
    showTab('pengaduan');
@endif
</script>

@include('footer')
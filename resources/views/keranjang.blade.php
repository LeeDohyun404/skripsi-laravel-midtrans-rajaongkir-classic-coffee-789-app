<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Keranjang Belanja - Classic 789</title>
</head>
<body class="bg-stone-50">
    @include('header')
    <div class="container mx-auto px-6 py-12 max-w-4xl">
        <h2 class="text-3xl font-serif italic mb-8 border-b pb-4">Keranjang Anda</h2>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border">
                <table class="w-full text-left">
                    <thead class="bg-stone-900 text-white text-[10px] uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @php $total = 0 @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <tr>
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <img src="{{ url('/foto-produk') . '?f=' . $details['image'] }}" class="w-16 h-16 rounded-xl object-cover border">
                                    <div>
                                        <p class="font-bold text-stone-800">{{ $details['name'] }}</p>
                                        <p class="text-xs text-amber-700">Rp {{ number_format($details['price']) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="/cart/update/{{ $id }}/minus" class="w-8 h-8 flex items-center justify-center bg-stone-100 rounded-full hover:bg-red-500 hover:text-white transition">-</a>
                                        <span class="font-bold">{{ $details['quantity'] }}</span>
                                        <a href="/cart/update/{{ $id }}/plus" class="w-8 h-8 flex items-center justify-center bg-stone-100 rounded-full hover:bg-green-500 hover:text-white transition">+</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold">
                                    Rp {{ number_format($details['price'] * $details['quantity']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-8 bg-stone-50 border-t flex flex-col items-end gap-4">

                    {{-- ── Info Voucher ── --}}
                    @auth
                        @php
                            // Validasi session voucher: cek apakah masih aktif di DB
                            if (session('voucher_active') && session('voucher_id')) {
                                $checkVoucher = \App\Models\Voucher::where('id', session('voucher_id'))
                                    ->where('status', 'active')
                                    ->where('expires_at', '>', now())
                                    ->first();
                                if (!$checkVoucher) {
                                    // Voucher sudah terpakai/expired, bersihkan session
                                    session()->forget(['voucher_active', 'voucher_id', 'voucher_code']);
                                }
                            }
                        @endphp

                        @if(session('voucher_active'))
                            {{-- Voucher sudah diaktifkan --}}
                            <div class="w-full bg-amber-50 border border-amber-300 rounded-2xl p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">🎉</span>
                                    <div>
                                        <p class="font-bold text-amber-800 text-sm">Voucher 1 Minuman Gratis Aktif!</p>
                                        <p class="text-xs text-amber-600">Minuman termurah di keranjang akan digratiskan</p>
                                    </div>
                                </div>
                                <form action="{{ route('voucher.deactivate') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-500 font-bold hover:text-red-700 border border-red-300 px-3 py-1.5 rounded-lg transition">
                                        ✕ Batal
                                    </button>
                                </form>
                            </div>
                            <p class="text-xs text-amber-700 w-full">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pembayaran hanya <strong>tunai (cash)</strong> saat menggunakan voucher.
                            </p>
                        @else
                            {{-- Cek apakah punya voucher tapi belum diaktifkan --}}
                            @php
                                $activeVoucherCart = \App\Models\Voucher::activeFor(Auth::id())->first();
                            @endphp
                            @if($activeVoucherCart)
                                <div class="w-full bg-stone-100 border border-stone-200 rounded-2xl p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🎟️</span>
                                        <div>
                                            <p class="font-bold text-stone-700 text-sm">Kamu punya voucher minuman gratis!</p>
                                            <p class="text-xs text-stone-500">Aktifkan di halaman profil untuk menggunakannya</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('profile') }}" class="text-xs text-amber-700 font-bold hover:text-amber-900 border border-amber-300 px-3 py-1.5 rounded-lg transition bg-amber-50">
                                        Lihat Voucher
                                    </a>
                                </div>
                            @endif
                        @endif
                    @endauth

                    {{-- ── Total ── --}}
                    @php
                        $cartHasMakananOrPaket = false;
                        foreach (session('cart', []) as $cid => $citem) {
                            $ccat = strtolower($citem['category'] ?? '');
                            if (empty($ccat) && is_numeric($cid)) {
                                $ccat = strtolower(\Illuminate\Support\Facades\DB::table('products')->where('id', $cid)->value('category') ?? '');
                            }
                            if ($ccat === 'makanan' || $ccat === 'paket') {
                                $cartHasMakananOrPaket = true;
                                break;
                            }
                        }
                    @endphp

                    @if($cartHasMakananOrPaket)
                        <div class="w-full bg-orange-50 border border-orange-200 rounded-2xl p-4 flex items-center gap-3 text-orange-800 text-xs sm:text-sm">
                            <i class="fas fa-utensils text-orange-600 text-lg shrink-0"></i>
                            <div>
                                <span class="font-bold">Info Pemesanan:</span>
                                Keranjangmu berisi produk makanan/paket. Pesanan ini hanya dapat dipesan untuk <strong>Makan di Sini (Dine-In)</strong> karena makanan tidak dapat dikirim via kurir.
                            </div>
                        </div>
                    @endif

                    <div class="text-right">
                        <p class="text-sm text-stone-500 uppercase tracking-widest">Total Pembayaran</p>
                        <h3 class="text-3xl font-black text-stone-900 mb-2">Rp {{ number_format($total) }}</h3>
                        @if(session('voucher_active'))
                            <p class="text-xs text-amber-600 font-semibold">* Diskon voucher dihitung saat checkout</p>
                        @endif
                    </div>

                    <div class="flex gap-4 w-full md:w-auto">
                        <a href="/produk" class="flex-1 md:flex-none text-center border-2 border-stone-900 px-8 py-4 rounded-2xl font-bold hover:bg-stone-900 hover:text-white transition">
                            KEMBALI BELANJA
                        </a>
                        <a href="/checkout_page" class="bg-black text-white px-8 py-4 rounded-2xl font-bold hover:bg-amber-600 transition">
                            {{ $cartHasMakananOrPaket ? 'LANJUT KE CHECKOUT' : 'LANJUT KE PENGIRIMAN' }}
                        </a>
                    </div>

                </div>
            </div>

        @else
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-stone-300">
                <i class="fas fa-shopping-basket text-6xl text-stone-200 mb-4"></i>
                <p class="text-stone-500 mb-6">Wah, keranjangmu masih kosong nih.</p>
                <a href="/produk" class="bg-stone-900 text-white px-8 py-4 rounded-2xl font-bold uppercase text-xs tracking-widest">Ayo Belanja!</a>
            </div>
        @endif

    </div>
</body>
</html>
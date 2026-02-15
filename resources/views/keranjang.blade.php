<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Keranjang Belanja - Classic 789</title>
</head>
<body class="bg-stone-50">
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
                                    <img src="{{ asset('images/'.$details['image']) }}" class="w-16 h-16 rounded-xl object-cover border">
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

                <div class="p-8 bg-stone-50 border-t flex flex-col items-end">
                    <p class="text-sm text-stone-500 uppercase tracking-widest">Total Pembayaran</p>
                    <h3 class="text-3xl font-black text-stone-900 mb-6">Rp {{ number_format($total) }}</h3>

                    <div class="flex gap-4 w-full md:w-auto">
                        <a href="/produk" class="flex-1 md:flex-none text-center border-2 border-stone-900 px-8 py-4 rounded-2xl font-bold hover:bg-stone-900 hover:text-white transition">
                            KEMBALI BELANJA
                        </a>
                        {{-- ✅ FIX: ganti checkout-page → checkout_page --}}
                        <a href="/checkout_page" class="bg-black text-white px-8 py-4 rounded-2xl font-bold hover:bg-amber-600 transition">
                            LANJUT KE PENGIRIMAN
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
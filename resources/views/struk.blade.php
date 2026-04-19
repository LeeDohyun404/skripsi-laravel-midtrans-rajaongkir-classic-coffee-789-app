{{-- resources/views/struk.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ substr($order->number, -8) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .struk-container {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            .page-wrap {
                padding: 0 !important;
            }
            @page {
                size: A5 portrait;
                margin: 8mm;
            }
        }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    </style>
</head>
<body class="bg-stone-100">

<div class="page-wrap container mx-auto py-8 px-4 max-w-sm">

    {{-- Tombol Aksi --}}
    <div class="flex gap-3 mb-5 no-print">
        <button onclick="window.print()" class="flex-1 bg-stone-900 text-white py-3 rounded-2xl font-bold hover:bg-amber-600 transition text-sm">
            <i class="fas fa-print mr-2"></i> Cetak Struk
        </button>
        <a href="/produk" class="flex-1 text-center bg-stone-100 text-stone-700 py-3 rounded-2xl font-bold hover:bg-stone-200 transition text-sm border border-stone-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Struk --}}
    <div class="struk-container bg-white rounded-3xl shadow-xl border border-stone-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-stone-900 text-white text-center py-5 px-6">
            <div class="text-amber-400 font-black text-xl tracking-tighter mb-0.5">CLASSIC<span class="text-white">789</span></div>
            <p class="text-stone-400 text-[10px] uppercase tracking-widest">Authentic Coffee</p>
            <div class="border-t border-stone-700 mt-3 pt-3 text-[10px] text-stone-400 space-y-0.5">
                <p><i class="fas fa-map-marker-alt text-amber-400 mr-1"></i> Jl. Kebanaran No.59, Kemangunan, Kebumen</p>
                <p><i class="fas fa-store text-amber-400 mr-1"></i> Outlet: Kapal Mendoan Alun-alun L2 A32</p>
                <p><i class="fas fa-phone text-amber-400 mr-1"></i> 0896-6950-5208</p>
            </div>
        </div>

        <div class="px-5 py-4">

            {{-- Nomor Antrian --}}
            <div class="text-center mb-4">
                <p class="text-[10px] text-stone-400 uppercase tracking-widest mb-0.5">Nomor Antrian</p>
                <div class="text-5xl font-black text-amber-600">{{ $nomorAntrian }}</div>
                <p class="text-[10px] text-stone-400 mt-0.5">#{{ substr($order->number, -8) }}</p>
            </div>

            {{-- Badge Voucher --}}
            @if($order->used_voucher)
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-center mb-3">
                <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">🎉 Menggunakan Voucher Minuman Gratis</p>
            </div>
            @endif

            <div class="border-t border-dashed border-stone-200 my-3"></div>

            {{-- Data Pemesan --}}
            <div class="mb-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-stone-400 mb-2">Data Pemesan</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Nama</span>
                        <span class="font-bold text-stone-800">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">No. HP</span>
                        <span class="font-bold text-stone-800">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Jenis Pesanan</span>
                        <span class="font-bold {{ $order->order_type == 'dine-in' ? 'text-blue-600' : 'text-orange-600' }}">
                            {{ $order->order_type == 'dine-in' ? '🍽️ Makan di Sini' : '🛵 Bawa Pulang' }}
                        </span>
                    </div>
                    @if($order->order_type != 'dine-in' && $order->address)
                    <div class="flex justify-between items-start">
                        <span class="text-stone-500">Alamat</span>
                        <span class="font-bold text-stone-800 text-right max-w-[60%] text-[10px]">{{ $order->address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="border-t border-dashed border-stone-200 my-3"></div>

            {{-- Detail Produk --}}
            <div class="mb-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-stone-400 mb-2">Detail Pesanan</p>
                <div class="space-y-2">
                    @foreach($items as $item)
                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <p class="font-bold text-stone-800">{{ $item->product_name }}</p>
                            <p class="text-[10px] text-stone-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-bold text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-dashed border-stone-200 my-3"></div>

            {{-- Ringkasan Harga --}}
            <div class="space-y-1.5 text-xs mb-3">
                <div class="flex justify-between">
                    <span class="text-stone-500">Subtotal</span>
                    <span class="font-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->ongkir > 0)
                <div class="flex justify-between">
                    <span class="text-stone-500">Ongkos Kirim</span>
                    <span class="font-bold">Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                </div>
                @if($order->courier_name)
                <div class="flex justify-between">
                    <span class="text-stone-500">Kurir</span>
                    <span class="font-bold text-stone-800 text-[10px]">{{ strtoupper($order->courier_name) }} - {{ strtoupper($order->courier_service) }}</span>
                </div>
                @endif
                @endif
                @if($order->used_voucher && $order->voucher_discount > 0)
                <div class="flex justify-between text-amber-600">
                    <span class="font-semibold">Diskon Voucher</span>
                    <span class="font-bold">- Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-stone-200">
                    <span class="font-black text-stone-800 text-sm">TOTAL</span>
                    <span class="font-black text-amber-600 text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Pembayaran</span>
                    <span class="font-bold text-xs {{ in_array($order->status, ['success','settlement','capture','completed']) ? 'text-green-600' : 'text-orange-500' }}">
                        <i class="fas {{ in_array($order->status, ['success','settlement','capture','completed']) ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                        {{ in_array($order->status, ['success','settlement','capture','completed']) ? 'LUNAS' : strtoupper($order->status) }}
                    </span>
                </div>
                @if($order->used_voucher)
                <div class="flex justify-between">
                    <span class="text-stone-500">Metode Bayar</span>
                    <span class="font-bold text-stone-700 text-xs">💵 Tunai (Cash)</span>
                </div>
                @endif
            </div>

            <div class="border-t border-dashed border-stone-200 my-3"></div>

            {{-- Footer --}}
            <div class="text-center text-[10px] text-stone-400 space-y-0.5">
                <p>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }} WIB</p>
                <p class="font-bold text-stone-600 mt-2">Terima kasih sudah memesan! ☕</p>
                <p>Struk ini adalah bukti pembayaran yang sah.</p>
            </div>

        </div>
    </div>

</div>
</body>
</html>
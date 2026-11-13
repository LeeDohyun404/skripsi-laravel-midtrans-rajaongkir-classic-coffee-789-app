<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // =====================================================
    // ✅ DASHBOARD — menampilkan statistik + daftar produk
    //    untuk modal Transaksi Baru
    // =====================================================
    public function dashboard()
    {
        // Statistik ringkasan
        $transaksiHariIni   = DB::table('orders')->whereDate('created_at', today())->count();
        $pendapatanHariIni  = (int) DB::table('orders')
            ->whereDate('created_at', today())
            ->whereIn('status', ['completed', 'success'])
            ->sum(DB::raw('CAST(total_price AS UNSIGNED)'));
        $transaksiMingguIni = DB::table('orders')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $pendapatanMingguIni = (int) DB::table('orders')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['completed', 'success'])
            ->sum(DB::raw('CAST(total_price AS UNSIGNED)'));

        // 10 transaksi terbaru hari ini
        $transaksiTerbaru = DB::table('orders')
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ✅ Daftar produk untuk pencarian di modal Transaksi Baru
        $produkList = DB::table('products')
            ->select('id', 'name', 'price', 'image_url')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'price'     => (int) $p->price,
                'image_url' => $p->image_url ?? null,
            ]);

        return view('kasir.dashboard', compact(
            'transaksiHariIni',
            'pendapatanHariIni',
            'transaksiMingguIni',
            'pendapatanMingguIni',
            'transaksiTerbaru',
            'produkList'
        ));
    }

    // =====================================================
    // ✅ STORE TRANSAKSI BARU (dipanggil via AJAX dari modal)
    //    POST /kasir/transaksi/store
    // =====================================================
    public function storeTransaksi(Request $request)
    {
        try {
            // Validasi minimal
            $request->validate([
                'customer_name'     => 'nullable|string|max:255',
                'whatsapp_number'   => 'nullable|string|max:20',  // ✅ TAMBAHAN
                'total_price'       => 'required|numeric|min:0',
                'items'             => 'required|array|min:1',
                'items.*.product_name'  => 'required|string',
                'items.*.price'         => 'required|numeric|min:0',
                'items.*.quantity'      => 'required|integer|min:1',
                'items.*.subtotal'      => 'required|numeric|min:0',
            ]);

            // Format nomor WhatsApp jika ada
            $waNumber = null;
            if ($request->filled('whatsapp_number')) {
                $wa = preg_replace('/\D/', '', $request->whatsapp_number);
                if (str_starts_with($wa, '0')) {
                    $wa = '62' . substr($wa, 1);
                } elseif (!str_starts_with($wa, '62')) {
                    $wa = '62' . $wa;
                }
                $waNumber = $wa;
            }

            // Hitung nomor antrian (reset setiap hari)
            $antrian = DB::table('orders')
                ->whereDate('created_at', today())
                ->count() + 1;

            // Generate nomor order: KSR-YYYYMMDD-XXXX
            $nomorOrder = 'KSR-' . now()->format('Ymd') . '-' . str_pad($antrian, 4, '0', STR_PAD_LEFT);

            // Simpan order
            $orderId = DB::table('orders')->insertGetId([
                'number'         => $nomorOrder,
                'customer_name'  => $request->customer_name ?? 'Umum',
                'customer_phone' => $waNumber,               // ✅ SIMPAN NOMOR WA KE customer_phone
                'total_price'    => $request->total_price,
                'subtotal'       => $request->total_price,
                'status'         => 'completed',             // langsung selesai (kasir offline)
                'order_type'     => 'dine-in',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Simpan order_items
            foreach ($request->items as $item) {
                DB::table('order_items')->insert([
                    'order_id'      => $orderId,
                    'product_name'  => $item['product_name'],
                    'product_image' => $item['product_image'] ?? null,
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                    'subtotal'      => $item['subtotal'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'order'   => [
                    'id'             => $orderId,
                    'number'         => $nomorOrder,
                    'antrian'        => $antrian,
                    'customer_name'  => $request->customer_name ?? 'Umum',
                    'customer_phone' => $waNumber,           // ✅ KEMBALIKAN KE FRONTEND JUGA
                    'total_price'    => (int) $request->total_price,
                    'created_at'     => now()->format('d/m/Y H:i'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // =====================================================
    // HALAMAN TRANSAKSI LENGKAP (tab kelola, riwayat, laporan)
    // =====================================================
    public function transaksi(Request $request)
    {
        // =====================================================
        // STATISTIK HEADER
        // =====================================================
        $totalTransaksiHariIni = DB::table('orders')
            ->whereDate('created_at', today())->count();

        $omzetHariIni = DB::table('orders')
            ->whereDate('created_at', today())
            ->whereIn('status', ['completed', 'success'])
            ->sum(DB::raw('CAST(total_price AS UNSIGNED)'));

        $totalPesananPending = DB::table('orders')
            ->where('status', 'pending')->count();

        $totalPesananSelesai = DB::table('orders')
            ->whereDate('created_at', today())
            ->whereIn('status', ['completed', 'success'])->count();

        // =====================================================
        // KELOLA PESANAN
        // =====================================================
        $pesanan = DB::table('orders')
            ->orderByDesc('created_at')
            ->paginate(10);

        // =====================================================
        // RIWAYAT TRANSAKSI
        // =====================================================
        $queryRiwayat = DB::table('orders')->orderByDesc('created_at');
        if ($request->filled('dari')) {
            $queryRiwayat->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryRiwayat->whereDate('created_at', '<=', $request->sampai);
        }
        $riwayat = $queryRiwayat->paginate(10, ['*'], 'riwayat_page');

        // =====================================================
        // LAPORAN PENJUALAN
        // =====================================================
        $periode      = $request->get('periode', 'hari');
        $queryLaporan = DB::table('orders')->whereIn('status', ['completed', 'success']);
        match ($periode) {
            'minggu' => $queryLaporan->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'bulan'  => $queryLaporan->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            default  => $queryLaporan->whereDate('created_at', today()),
        };

        $laporanOmzet       = (int) $queryLaporan->sum(DB::raw('CAST(total_price AS UNSIGNED)'));
        $laporanJumlahOrder = $queryLaporan->count();
        $laporanRataRata    = $laporanJumlahOrder > 0 ? $laporanOmzet / $laporanJumlahOrder : 0;

        // =====================================================
        // GRAFIK PENJUALAN - Per Jam (Hari Ini)
        // =====================================================
        $rawHari = DB::table('orders')
            ->selectRaw('HOUR(created_at) as jam, SUM(CAST(total_price AS UNSIGNED)) as omzet, COUNT(*) as jml_order')
            ->whereDate('created_at', today())
            ->whereIn('status', ['completed', 'success'])
            ->groupBy('jam')->get()->keyBy('jam');

        $hariLabels = $hariOmzet = $hariOrder = [];
        for ($h = 0; $h < 24; $h++) {
            $hariLabels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $hariOmzet[]  = (int) ($rawHari[$h]->omzet ?? 0);
            $hariOrder[]  = (int) ($rawHari[$h]->jml_order ?? 0);
        }

        // GRAFIK PENJUALAN - Per Hari (Minggu Ini)
        $rawMinggu = DB::table('orders')
            ->selectRaw('DATE(created_at) as tgl, SUM(CAST(total_price AS UNSIGNED)) as omzet, COUNT(*) as jml_order')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['completed', 'success'])
            ->groupBy('tgl')->get()->keyBy('tgl');

        $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $mingguLabels = $mingguOmzet = $mingguOrder = [];
        for ($d = 0; $d < 7; $d++) {
            $tgl            = now()->startOfWeek()->addDays($d)->toDateString();
            $mingguLabels[] = $namaHari[$d];
            $mingguOmzet[]  = (int) ($rawMinggu[$tgl]->omzet ?? 0);
            $mingguOrder[]  = (int) ($rawMinggu[$tgl]->jml_order ?? 0);
        }

        // GRAFIK PENJUALAN - Per Tanggal (Bulan Ini)
        $rawBulan = DB::table('orders')
            ->selectRaw('DAY(created_at) as tgl, SUM(CAST(total_price AS UNSIGNED)) as omzet, COUNT(*) as jml_order')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['completed', 'success'])
            ->groupBy('tgl')->get()->keyBy('tgl');

        $hariDalamBulan = now()->daysInMonth;
        $bulanLabels = $bulanOmzet = $bulanOrder = [];
        for ($t = 1; $t <= $hariDalamBulan; $t++) {
            $bulanLabels[] = (string) $t;
            $bulanOmzet[]  = (int) ($rawBulan[$t]->omzet ?? 0);
            $bulanOrder[]  = (int) ($rawBulan[$t]->jml_order ?? 0);
        }

        $grafikPenjualan = [
            'hari'   => ['labels' => $hariLabels,  'omzet' => $hariOmzet,  'order' => $hariOrder],
            'minggu' => ['labels' => $mingguLabels, 'omzet' => $mingguOmzet, 'order' => $mingguOrder],
            'bulan'  => ['labels' => $bulanLabels,  'omzet' => $bulanOmzet,  'order' => $bulanOrder],
        ];

        // =====================================================
        // GRAFIK & TABEL PRODUK TERLARIS (dari order_items)
        // =====================================================
        $topProduk = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['completed', 'success'])
            ->selectRaw('order_items.product_name as name, SUM(order_items.quantity) as total_terjual, SUM(order_items.subtotal) as total_pendapatan')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $grafikProduk = [
            'labels' => $topProduk->pluck('name')->toArray(),
            'values' => $topProduk->pluck('total_terjual')->map(fn($v) => (int)$v)->toArray(),
        ];

        // =====================================================
        // GRAFIK & TABEL PELANGGAN PALING SERING BELI
        // =====================================================
        $rawPelanggan = DB::table('orders')
            ->selectRaw('customer_name, COUNT(*) as total_order')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->whereIn('status', ['success', 'completed'])
            ->groupBy('customer_name')
            ->orderByDesc('total_order')
            ->limit(10)
            ->get();

        $grafikPelanggan = [
            'labels' => $rawPelanggan->pluck('customer_name')->toArray(),
            'values' => $rawPelanggan->pluck('total_order')->map(fn($v) => (int)$v)->toArray(),
        ];

        $topPelanggan = DB::table('orders')
            ->selectRaw('customer_name, MAX(customer_email) as customer_email, MAX(customer_phone) as customer_phone, COUNT(*) as total_order, SUM(CAST(total_price AS UNSIGNED)) as total_belanja')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->whereIn('status', ['success', 'completed'])
            ->groupBy('customer_name')
            ->orderByDesc('total_order')
            ->limit(10)
            ->get();

        return view('kasir.transaksi', compact(
            'totalTransaksiHariIni', 'omzetHariIni',
            'totalPesananPending',   'totalPesananSelesai',
            'pesanan',   'riwayat',
            'laporanOmzet', 'laporanJumlahOrder', 'laporanRataRata',
            'topProduk', 'topPelanggan',
            'grafikPenjualan', 'grafikProduk', 'grafikPelanggan'
        ));
    }

    // =====================================================
    // DETAIL PESANAN BY ID (untuk modal Kelola Pesanan)
    // =====================================================
    public function detailPesanan($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan.'], 404);
        }

        $items = DB::table('order_items')
            ->where('order_id', $id)
            ->get()
            ->map(fn($i) => [
                'product_name'  => $i->product_name,
                'product_image' => $i->product_image,
                'price'         => (int) $i->price,
                'quantity'      => (int) $i->quantity,
                'subtotal'      => (int) $i->subtotal,
            ]);

        return response()->json([
            'number'          => $order->number,
            'customer_name'   => $order->customer_name ?? '-',
            'customer_phone'  => $order->customer_phone ?? '-',  // ✅ nomor WA tampil di sini
            'customer_email'  => $order->customer_email ?? '-',
            'order_type'      => $order->order_type ?? '-',
            'status'          => $order->status,
            'address'         => $order->address ?? '-',
            'notes'           => $order->notes ?? '-',
            'courier_name'    => $order->courier_name ?? null,
            'courier_service' => $order->courier_service ?? null,
            'ongkir'          => (int) ($order->ongkir ?? 0),
            'subtotal'        => (int) ($order->subtotal ?? 0),
            'total_price'     => (int) $order->total_price,
            'items'           => $items,
        ]);
    }

    // =====================================================
    // DETAIL PESANAN PER PELANGGAN
    // =====================================================
    public function detailPelanggan(Request $request)
    {
        $customerName = $request->customer_name;

        $orders = DB::table('orders')
            ->where('customer_name', $customerName)
            ->orderByDesc('created_at')
            ->get();

        $result = $orders->map(function ($order) {
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            return [
                'number'          => $order->number,
                'status'          => $order->status,
                'order_type'      => $order->order_type,
                'courier_name'    => $order->courier_name ?? '-',
                'courier_service' => $order->courier_service ?? '-',
                'ongkir'          => (int) ($order->ongkir ?? 0),
                'subtotal'        => (int) ($order->subtotal ?? 0),
                'total_price'     => (int) $order->total_price,
                'address'         => $order->address ?? '-',
                'created_at'      => $order->created_at,
                'items'           => $items->map(fn($i) => [
                    'product_name'  => $i->product_name,
                    'product_image' => $i->product_image,
                    'price'         => (int) $i->price,
                    'quantity'      => (int) $i->quantity,
                    'subtotal'      => (int) $i->subtotal,
                ]),
            ];
        });

        return response()->json($result);
    }

    // =====================================================
    // UPDATE STATUS PESANAN
    // =====================================================
    public function updatePesanan(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled,success']);

        $newStatus = $request->status;
        if ($newStatus === 'completed') {
            $newStatus = 'success';
        }

        $order = \App\Models\Order::find($id);
        if ($order) {
            $order->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

            // Jika status menjadi sukses / lunas
            if (in_array($newStatus, ['success', 'completed'])) {
                // Tandai voucher terpakai jika ada
                if ($order->used_voucher && $order->voucher_id) {
                    $voucher = \App\Models\Voucher::find($order->voucher_id);
                    if ($voucher) {
                        \App\Services\CoffeeBeanService::markVoucherUsed($order, $voucher);
                    }
                }

                // Tambahkan progress biji kopi untuk item minuman yang dibayar
                \App\Services\CoffeeBeanService::processOrder($order);

                // Catat tracking pesanan
                \App\Models\OrderTracking::create([
                    'order_id'   => $order->id,
                    'status'     => 'selesai',
                    'keterangan' => 'Pembayaran dikonfirmasi kasir (Lunas).',
                    'updated_by' => \Illuminate\Support\Facades\Auth::guard('karyawan')->user()->nama ?? 'Kasir',
                ]);
            }
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
    // =====================================================
// TAMBAHKAN METHOD INI DI DALAM CLASS KasirController
// Letakkan setelah method updatePesanan() paling bawah
// =====================================================
 
public function laporanDetail(Request $request)
{
    $rentang    = $request->get('rentang', 'hari');
    $tanggal    = $request->get('tanggal', today()->toDateString());
    $namaProduk = trim((string) $request->get('nama_produk', ''));

    // Tentukan range tanggal berdasarkan rentang yang dipilih
    $dari   = null;
    $sampai = null;

    switch ($rentang) {
        case 'minggu':
            $dari   = now()->startOfWeek()->toDateString();
            $sampai = now()->endOfWeek()->toDateString();
            break;
        case 'bulan':
            $dari   = now()->startOfMonth()->toDateString();
            $sampai = now()->endOfMonth()->toDateString();
            break;
        case 'tahun':
            $dari   = now()->startOfYear()->toDateString();
            $sampai = now()->endOfYear()->toDateString();
            break;
        case 'semua':
            // Tidak ada batasan tanggal sama sekali
            $dari   = null;
            $sampai = null;
            break;
        default: // hari
            $dari   = $tanggal;
            $sampai = $tanggal;
            break;
    }

    // Ambil semua order dalam rentang yang dipilih (status selesai)
    $ordersQuery = DB::table('orders')
        ->whereIn('status', ['completed', 'success'])
        ->orderBy('created_at');

    if ($dari && $sampai) {
        $ordersQuery->whereDate('created_at', '>=', $dari)
                    ->whereDate('created_at', '<=', $sampai);
    }

    $orders   = $ordersQuery->get();
    $orderIds = $orders->pluck('id');

    // Ambil semua order_items dari order tersebut, filter nama produk jika diisi
    $itemsQuery = DB::table('order_items')->whereIn('order_id', $orderIds);

    if ($namaProduk !== '') {
        $itemsQuery->where('product_name', 'like', '%' . $namaProduk . '%');
    }

    $allItems = $itemsQuery->get();

    // Jika filter nama produk aktif, hanya order yang memuat produk tsb yang dihitung
    if ($namaProduk !== '') {
        $matchedOrderIds = $allItems->pluck('order_id')->unique();
        $orders = $orders->whereIn('id', $matchedOrderIds);
    }

    // Kelompokkan berdasarkan tanggal
    $grouped = $orders->groupBy(fn($o) => \Carbon\Carbon::parse($o->created_at)->toDateString());

    $result = [];

    foreach ($grouped as $tgl => $ordersPerHari) {
        $orderIdsHari = $ordersPerHari->pluck('id');
        $itemsHari    = $allItems->whereIn('order_id', $orderIdsHari);

        // Hitung total produk terjual per nama produk
        $produkSummary = $itemsHari->groupBy('product_name')->map(fn($items) => [
            'nama'     => $items->first()->product_name,
            'jumlah'   => $items->sum('quantity'),
        ])->values();

        // Daftar nama pembeli unik
        $pembeli = $ordersPerHari
            ->pluck('customer_name')
            ->filter()
            ->unique()
            ->values();

        // Omzet: kalau filter produk aktif, jumlahkan subtotal item yang cocok saja.
        // Kalau tidak ada filter, gunakan total_price order (omzet keseluruhan order).
        $omzet = $namaProduk !== ''
            ? (int) $itemsHari->sum('subtotal')
            : (int) $ordersPerHari->sum('total_price');

        $result[] = [
            'tanggal'        => \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y'),
            'total_terjual'  => $itemsHari->sum('quantity'),
            'total_omzet'    => $omzet,
            'total_order'    => $ordersPerHari->count(),
            'produk'         => $produkSummary,
            'pembeli'        => $pembeli,
        ];
    }

    return response()->json([
        'rentang'     => $rentang,
        'nama_produk' => $namaProduk,
        'dari'        => $dari,
        'sampai'      => $sampai,
        'data'        => $result,
    ]);
}
}
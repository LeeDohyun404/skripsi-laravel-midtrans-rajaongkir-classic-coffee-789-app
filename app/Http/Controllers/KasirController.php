<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
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
            $tgl           = now()->startOfWeek()->addDays($d)->toDateString();
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
            ->whereIn('status', ['success', 'completed']) // ← tambah ini
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
            ->whereIn('status', ['success', 'completed']) // ← tambah ini
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
    // DETAIL PESANAN PER PELANGGAN (untuk modal)
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
                    'price'         => $i->price,
                    'quantity'      => $i->quantity,
                    'subtotal'      => $i->subtotal,
                ]),
            ];
        });

        return response()->json($result);
    }

    public function updatePesanan(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled,success']);

        DB::table('orders')->where('id', $id)->update([
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status pesanan diperbarui!');
    }
}
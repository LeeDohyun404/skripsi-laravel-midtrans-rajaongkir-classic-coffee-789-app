<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderTracking;

class OperatorController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // DASHBOARD & STOK (method lama — tetap ada)
    // ══════════════════════════════════════════════════════════

    public function dashboard()
    {
        $totalProduk = DB::table('products')->count();
        $stokRendah = DB::table('products')->whereBetween('stock', [1, 10])->count();
        $stokAman = DB::table('products')->where('stock', '>', 10)->count();
        $stokHampirHabis = DB::table('products')
            ->select('name as nama_produk', 'category as kategori', 'stock as stok')
            ->whereBetween('stock', [1, 10])
            ->orderBy('stock')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->satuan = 'pcs';
                return $item;
            });

        return view('operator.dashboard', compact(
            'totalProduk',
            'stokRendah',
            'stokAman',
            'stokHampirHabis'
        ));
    }

    public function stok(Request $request)
    {
        $query = DB::table('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        if ($request->filled('stok_filter')) {
            match ($request->stok_filter) {
                'habis'  => $query->where('stock', '<=', 0),
                'rendah' => $query->whereBetween('stock', [1, 10]),
                'aman'   => $query->where('stock', '>', 10),
                default  => null,
            };
        }

        $products     = $query->paginate(50);
        $kategoriList = DB::table('products')->distinct()->pluck('category')->filter();

        $totalProduk = DB::table('products')->count();
        $stokHabis   = DB::table('products')->where('stock', '<=', 0)->count();
        $stokRendah  = DB::table('products')->whereBetween('stock', [1, 10])->count();
        $stokAman    = DB::table('products')->where('stock', '>', 10)->count();

        return view('operator.stok', compact(
            'products', 'kategoriList',
            'totalProduk', 'stokHabis', 'stokRendah', 'stokAman'
        ));
    }

    public static function pastikanKolomDescription(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('products') && !\Illuminate\Support\Facades\Schema::hasColumn('products', 'description')) {
            try {
                \Illuminate\Support\Facades\Schema::table('products', function ($table) {
                    $table->text('description')->nullable()->after('category');
                });
            } catch (\Throwable $e) {}
        }
    }

    public function storeProduk(Request $request)
    {
        self::pastikanKolomDescription();

        $request->validate([
            'name'        => 'required|max:100',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'stock'       => 'nullable|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        $image = 'default.jpg';
        if ($request->hasFile('image')) {
            $image = ProductImageController::simpan($request->file('image'));
        }

        DB::table('products')->insert([
            'name'        => $request->name,
            'price'       => $request->price,
            'category'    => $request->category,
            'description' => $request->description,
            'stock'       => $request->stock ?? 0,
            'image_url'   => $image,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function updateProduk(Request $request)
    {
        self::pastikanKolomDescription();

        $request->validate([
            'id'          => 'required',
            'name'        => 'required',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'price'       => $request->price,
            'category'    => $request->category,
            'description' => $request->description,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = ProductImageController::simpan($request->file('image'));
        }

        DB::table('products')->where('id', $request->id)->update($data);

        return back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function updateStok(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'stock'      => 'required|integer|min:0',
        ]);

        DB::table('products')->where('id', $request->product_id)->update([
            'stock'      => $request->stock,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Stok berhasil diperbarui!');
    }

    public function destroyProduk($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return back()->with('success', 'Produk berhasil dihapus!');
    }

    // ══════════════════════════════════════════════════════════
    // TRACKING (method baru)
    // ══════════════════════════════════════════════════════════

    public function tracking(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        $query = Order::with(['items', 'trackings'])
                      ->where('order_type', 'take-away')
                      ->whereNotIn('status', ['pending'])
                      ->orderBy('created_at', 'desc');

        if ($filter !== 'semua') {
            $query->where('tracking_status', $filter);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('operator.tracking', compact('orders'));
    }

    public function updateTracking(Request $request, $orderId)
    {
        $request->validate([
            'status'     => 'required|in:dikemas,dikirim_ke_pengirim,dikirim,selesai,batal',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $order    = Order::findOrFail($orderId);
        $operator = Auth::guard('karyawan')->user();

        $validNextStatus = $this->getValidNextStatuses($order->tracking_status ?? 'dibuat');
        if (!in_array($request->status, $validNextStatus)) {
            return back()->with('error', 'Status tidak valid. Tidak bisa mundur ke status sebelumnya.');
        }

        $newStatus  = $request->status;
        $keterangan = $request->keterangan;

        if (empty($keterangan)) {
            $keterangan = OrderTracking::$statusConfig[$newStatus]['desc'] ?? '';
        }

        $order->update(['tracking_status' => $newStatus]);

        OrderTracking::create([
            'order_id'   => $order->id,
            'status'     => $newStatus,
            'keterangan' => $keterangan,
            'updated_by' => $operator->nama ?? 'Operator',
        ]);

        if ($newStatus === 'selesai') {
            $order->update(['status' => 'success']);
        }

        $cfg = OrderTracking::getConfig($newStatus);
        return back()->with('success', '✅ Status pesanan #' . $order->number . ' berhasil diperbarui ke "' . $cfg['label'] . '".');
    }

    private function getValidNextStatuses(string $current): array
    {
        $flow = [
            'dibuat'              => ['dikemas', 'batal'],
            'dikemas'             => ['dikirim_ke_pengirim', 'batal'],
            'dikirim_ke_pengirim' => ['dikirim', 'batal'],
            'dikirim'             => ['selesai', 'batal'],
            'selesai'             => [],
            'batal'               => [],
        ];

        return $flow[$current] ?? [];
    }

    // ══════════════════════════════════════════════════════════
    // PENGADUAN
    // ══════════════════════════════════════════════════════════

    public function pengaduan(Request $request)
    {
        // Pastikan semua foto bukti di disk tersinkron ke DB MySQL
        $this->syncComplaintImages();

        $filter = $request->get('filter', 'semua');

        $query = \App\Models\OrderComplaint::with('order')
                    ->orderBy('created_at', 'desc');

        if ($filter !== 'semua') {
            $query->where('status', $filter);
        }

        $complaints    = $query->paginate(10)->withQueryString();
        $totalMenunggu = \App\Models\OrderComplaint::where('status', 'menunggu')->count();

        return view('operator.complaint', compact('complaints', 'totalMenunggu'));
    }

    public function updatePengaduan(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:menunggu,diproses,selesai,ditolak',
            'catatan_operator' => 'nullable|string|max:1000',
        ]);

        $complaint = \App\Models\OrderComplaint::findOrFail($id);
        $complaint->update([
            'status'           => $request->status,
            'catatan_operator' => $request->catatan_operator,
        ]);

        $cfg = \App\Models\OrderComplaint::getStatus($request->status);
        return back()->with('success', '✅ Pengaduan berhasil diupdate ke "' . $cfg['label'] . '".');
    }

    /**
     * Sinkronkan foto komplain dari public/images/complaints ke database MySQL.
     */
    private function syncComplaintImages(): void
    {
        try {
            $dirs = [
                public_path('images/complaints'),
                storage_path('app/public/complaints'),
            ];

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) continue;
                $files = glob($dir . '/*.*');
                foreach ($files as $filePath) {
                    if (!is_file($filePath)) continue;
                    $filename = basename($filePath);
                    $exists = \Illuminate\Support\Facades\DB::table('product_images')->where('filename', $filename)->exists();
                    if (!$exists) {
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $mime = match($ext) {
                            'png'   => 'image/png',
                            'webp'  => 'image/webp',
                            'gif'   => 'image/gif',
                            default => 'image/jpeg',
                        };
                        \Illuminate\Support\Facades\DB::table('product_images')->insert([
                            'filename'   => $filename,
                            'mime'       => $mime,
                            'data'       => base64_encode(file_get_contents($filePath)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {}
    }
}
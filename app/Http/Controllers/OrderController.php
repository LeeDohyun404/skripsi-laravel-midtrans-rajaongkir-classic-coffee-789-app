<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\CoffeeBeanService;
use App\Services\PaymentSyncService;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $order_id    = 'ORDER-' . time() . '-' . rand(100, 999);
        $phone       = $request->phone;
        $subtotal    = (int) $request->subtotal;
        $address     = $request->address;
        $order_type  = $request->order_type;
        $ongkir      = (int) ($request->ongkir ?? 0);
        $courierSvc  = $request->courier_service ?? null;
        $courierName = $request->courier_name ?? null;

        $cart = session()->get('cart', []);

        // ── Validasi Produk Makanan & Paket: HANYA boleh dipesan di tempat (Dine-In) ──
        $hasMakananOrPaket = false;
        foreach ($cart as $pid => $item) {
            $cat = strtolower($item['category'] ?? '');
            if (empty($cat) && is_numeric($pid)) {
                $cat = strtolower(\Illuminate\Support\Facades\DB::table('products')->where('id', $pid)->value('category') ?? '');
            }
            if ($cat === 'makanan' || $cat === 'paket') {
                $hasMakananOrPaket = true;
                break;
            }
        }

        if ($hasMakananOrPaket && $order_type !== 'dine-in') {
            return back()->with('error', 'Pesanan yang mengandung produk makanan atau paket hanya bisa dipesan di tempat (Makan di Sini / Dine-In), tidak dapat dikirim via kurir.');
        }

        // ── Validasi voucher: hanya boleh untuk dine-in & keranjang HANYA minuman ──
        $hasVoucherSession = session('voucher_active') && session('voucher_id');

        if ($hasVoucherSession) {
            // Voucher hanya bisa digunakan untuk pesanan dine-in
            if ($order_type !== 'dine-in') {
                return back()->with('error', 'Voucher hanya bisa digunakan untuk pesanan Makan di Sini (Dine-In).');
            }

            // Voucher hanya bisa digunakan jika keranjang HANYA berisi minuman
            $hasNonMinuman = false;
            foreach ($cart as $pid => $item) {
                $cat = strtolower($item['category'] ?? '');
                if (empty($cat) && is_numeric($pid)) {
                    $cat = strtolower(\Illuminate\Support\Facades\DB::table('products')->where('id', $pid)->value('category') ?? '');
                }
                if ($cat !== 'minuman') {
                    $hasNonMinuman = true;
                    break;
                }
            }

            if ($hasNonMinuman) {
                return back()->with('error', 'Voucher hanya bisa digunakan untuk pesanan minuman saja. Hapus item makanan/paket dari keranjang atau nonaktifkan voucher di halaman profil.');
            }
        }

        // ── Terapkan voucher jika aktif ──────────────────────────────────
        $voucherDiscount = 0;
        $voucherModel    = null;
        $usedVoucher     = false;

        if (session('voucher_active') && session('voucher_id')) {
            $voucherModel = Voucher::where('id', session('voucher_id'))
                                   ->where('status', 'active')
                                   ->where('expires_at', '>', now())
                                   ->first();

            if ($voucherModel) {
                $drinkPrices = collect($cart)
                    ->filter(fn($item) =>
                        isset($item['category']) &&
                        strtolower($item['category']) === 'minuman'
                    )
                    ->pluck('price')
                    ->map(fn($p) => (int) $p);

                if ($drinkPrices->isNotEmpty()) {
                    $voucherDiscount = $drinkPrices->min();
                    $usedVoucher     = true;
                }
            }
        }

        $total = max(0, $subtotal + $ongkir - $voucherDiscount);

        // ── Simpan order ke database ─────────────────────────────────────
        $orderId = DB::table('orders')->insertGetId([
            'number'             => $order_id,
            'total_price'        => $total,
            'subtotal'           => $subtotal,
            'quantity'           => array_sum(array_column($cart, 'quantity')),
            'status'             => ($usedVoucher && $total == 0) ? 'claimed' : 'pending',
            'snap_token'         => null,
            'customer_name'      => Auth::user()->name,
            'customer_email'     => Auth::user()->email,
            'customer_phone'     => $phone,
            'order_type'         => $order_type,
            'courier_name'       => $courierName,
            'courier_service'    => $courierSvc,
            'ongkir'             => $ongkir,
            'address'            => $address,
            'notes'              => $request->notes ?? null,
            'used_voucher'       => $usedVoucher ? 1 : 0,
            'voucher_id'         => $usedVoucher ? $voucherModel->id : null,
            'voucher_discount'   => $voucherDiscount,
            'exclude_from_stats' => $usedVoucher ? 1 : 0,
            'tracking_status'    => 'dibuat',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // ── Simpan order items ───────────────────────────────────────────
        foreach ($cart as $productId => $item) {
            DB::table('order_items')->insert([
                'order_id'      => $orderId,
                'product_id'    => is_numeric($productId) ? (int) $productId : null,
                'product_name'  => $item['name'],
                'product_image' => $item['image'] ?? null,
                'price'         => (int) $item['price'],
                'quantity'      => (int) $item['quantity'],
                'subtotal'      => (int) $item['price'] * (int) $item['quantity'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // ── Simpan tracking awal ─────────────────────────────────────────
        OrderTracking::create([
            'order_id'   => $orderId,
            'status'     => 'dibuat',
            'keterangan' => 'Pesanan berhasil dibuat.',
            'updated_by' => 'Sistem',
        ]);

        // ── Jika pakai voucher dan total = 0 ─────────────────────────────
        if ($usedVoucher && $total == 0) {
            $orderModel = Order::find($orderId);
            if ($orderModel && $voucherModel) {
                CoffeeBeanService::markVoucherUsed($orderModel, $voucherModel);
            }

            session()->forget(['cart', 'voucher_active', 'voucher_id', 'voucher_code']);

            return redirect()->route('orders.struk', $orderId)
                ->with('success', '🎉 Pesanan berhasil diklaim! Tunjukkan struk ini ke kasir untuk pembayaran tunai.');
        }

        // ── Jika pakai voucher tapi total > 0 → bayar tunai (tanpa Midtrans) ──
        if ($usedVoucher && $total > 0) {
            $orderModel = Order::find($orderId);
            if ($orderModel && $voucherModel) {
                CoffeeBeanService::markVoucherUsed($orderModel, $voucherModel);
            }

            session()->forget(['cart', 'voucher_active', 'voucher_id', 'voucher_code']);

            return redirect()->route('orders.struk', $orderId)
                ->with('success', '🎟️ Voucher diterapkan! Tunjukkan struk ini ke kasir. Sisa bayar Rp ' . number_format($total, 0, ',', '.') . ' secara tunai.');
        }

        // ── Proses Midtrans ──────────────────────────────────────────────
        $itemDetails = [];
        foreach ($cart as $item) {
            $itemDetails[] = [
                'id'       => 'prod-' . ($item['name'] ?? 'item'),
                'price'    => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'name'     => substr($item['name'], 0, 50),
            ];
        }

        if ($ongkir > 0) {
            $itemDetails[] = [
                'id'       => 'ongkir',
                'price'    => $ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . $courierName . ' ' . $courierSvc . ')',
            ];
        }

        if ($usedVoucher && $voucherDiscount > 0) {
            $itemDetails[] = [
                'id'       => 'voucher-diskon',
                'price'    => -$voucherDiscount,
                'quantity' => 1,
                'name'     => 'Diskon Voucher Minuman Gratis',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order_id,
                'gross_amount' => $total,
            ],
            'item_details'     => $itemDetails,
            'notification_url' => url('/api/midtrans-callback'),
            'callbacks'        => [
                'finish' => url('/orders/' . $orderId . '/struk'),
                'error'  => url('/checkout_page?error=payment_failed'),
                'pending'=> url('/orders/' . $orderId . '/struk'),
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
                'phone'      => $phone,
            ],
        ];

        if ($order_type === 'take-away' && $address) {
            $params['customer_details']['shipping_address'] = [
                'first_name'   => Auth::user()->name,
                'address'      => $address,
                'city'         => 'Indonesia',
                'country_code' => 'IDN',
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($params);

            DB::table('orders')->where('id', $orderId)->update([
                'snap_token' => $snapToken,
                'updated_at' => now(),
            ]);

            session()->forget('cart');

            $order = DB::table('orders')->where('id', $orderId)->first();

            return view('checkout', compact('snapToken', 'order_id', 'order', 'orderId'));

        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    private function sendNotifications($orderId, $customerName, $total, $orderType, $phone)
    {
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@classic789.com');
            Notification::route('mail', $adminEmail)
                ->notify(new NewOrderNotification($orderId, $customerName, $total, $orderType, $phone));
            Log::info('✅ Email sent for order: ' . $orderId);
        } catch (\Exception $e) {
            Log::error('❌ Email failed: ' . $e->getMessage());
        }
    }

    /**
     * Midtrans webhook callback
     */
    public function notificationHandler(Request $request)
    {
        $payload  = json_decode($request->getContent(), true) ?: [];
        $order_id = $payload['order_id'] ?? null;
        $status   = $payload['transaction_status'] ?? null;

        Log::info('Midtrans Callback:', ['order_id' => $order_id, 'status' => $status]);

        if (!$order_id) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Verifikasi signature supaya callback palsu tidak bisa melunasi order
        $expected = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            config('services.midtrans.serverKey')
        );
        if (!hash_equals($expected, (string) ($payload['signature_key'] ?? ''))) {
            Log::warning('Midtrans signature tidak cocok', ['order_id' => $order_id]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('number', $order_id)->first();
        if ($order) {
            PaymentSyncService::apply($order, $status, $payload['fraud_status'] ?? null);
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Konfirmasi order cash (untuk order pakai voucher yang bayar tunai)
     */
    public function confirmCash($orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->where('used_voucher', 1)->first();

        if (!$order) {
            return back()->with('error', 'Order tidak ditemukan.');
        }

        DB::table('orders')->where('id', $orderId)->update([
            'status'     => 'success',
            'updated_at' => now(),
        ]);

        if ($order->voucher_id) {
            $orderModel = Order::find($orderId);
            $voucher    = Voucher::find($order->voucher_id);
            if ($orderModel && $voucher) {
                CoffeeBeanService::markVoucherUsed($orderModel, $voucher);
            }
        }

        return back()->with('success', 'Order #' . $order->number . ' dikonfirmasi lunas.');
    }

    /**
     * Konfirmasi pesanan diterima oleh pelanggan
     */
    public function konfirmasiSelesai($orderId)
    {
        $user  = Auth::user();
        $order = Order::where('id', $orderId)
                      ->where('customer_email', $user->email)
                      ->where('tracking_status', 'dikirim')
                      ->firstOrFail();

        $order->update([
            'tracking_status' => 'selesai',
            'status'          => 'success',
        ]);

        OrderTracking::create([
            'order_id'   => $order->id,
            'status'     => 'selesai',
            'keterangan' => 'Pesanan dikonfirmasi diterima oleh pelanggan.',
            'updated_by' => $user->name,
        ]);

        return back()->with('success', '✅ Terima kasih! Pesanan ' . $order->number . ' telah dikonfirmasi selesai.');
    }

    /**
     * Tampilkan struk
     */
    public function struk($orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            abort(404, 'Order tidak ditemukan.');
        }

        // Jaga-jaga kalau callback Midtrans belum masuk: tanya status langsung ke Midtrans
        if ($order->status === 'pending' && !$order->used_voucher) {
            $orderModel = Order::find($orderId);
            if ($orderModel && PaymentSyncService::syncFromMidtrans($orderModel)) {
                $order = DB::table('orders')->where('id', $orderId)->first();
            }
        }

        $items        = DB::table('order_items')->where('order_id', $orderId)->get();
        $nomorAntrian = str_pad($order->id % 100, 2, '0', STR_PAD_LEFT);

        return view('struk', compact('order', 'items', 'nomorAntrian'));
    }

    /**
     * Refresh snap_token
     */
    public function refreshToken(Request $request, $orderId)
    {
        $user  = Auth::user();
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('customer_email', $user->email)
            ->where('status', 'pending')
            ->where('used_voucher', 0)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan atau sudah tidak pending.'], 404);
        }

        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $items       = DB::table('order_items')->where('order_id', $orderId)->get();
        $itemDetails = [];
        foreach ($items as $item) {
            $itemDetails[] = [
                'id'       => 'prod-' . $item->product_name,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product_name, 0, 50),
            ];
        }

        if ($order->ongkir > 0) {
            $itemDetails[] = [
                'id'       => 'ongkir',
                'price'    => (int) $order->ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . $order->courier_name . ' ' . $order->courier_service . ')',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->number,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details'     => $itemDetails,
            'notification_url' => url('/api/midtrans-callback'),
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            DB::table('orders')->where('id', $orderId)->update([
                'snap_token' => $snapToken,
                'updated_at' => now(),
            ]);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memperbarui token pembayaran.'], 500);
        }
    }
}
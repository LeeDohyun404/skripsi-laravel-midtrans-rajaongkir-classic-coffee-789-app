<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use App\Models\Voucher;
use App\Services\CoffeeBeanService;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
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
                // Cari minuman dengan harga unit paling murah di keranjang
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

        // Total akhir setelah diskon
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
            // kolom voucher
            'used_voucher'       => $usedVoucher ? 1 : 0,
            'voucher_id'         => $usedVoucher ? $voucherModel->id : null,
            'voucher_discount'   => $voucherDiscount,
            'exclude_from_stats' => $usedVoucher ? 1 : 0,
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

        // ── Jika pakai voucher dan total = 0: cash only, tidak proses Midtrans ─────────
        if ($usedVoucher && $total == 0) {
            // Tandai voucher sebagai terpakai di database
            $orderModel = \App\Models\Order::find($orderId);
            if ($orderModel && $voucherModel) {
                CoffeeBeanService::markVoucherUsed($orderModel, $voucherModel);
            }

            session()->forget(['cart', 'voucher_active', 'voucher_id', 'voucher_code']);

            return redirect()->route('orders.struk', $orderId)
                ->with('success', '🎉 Pesanan berhasil diklaim! Tunjukkan struk ini ke kasir untuk pembayaran tunai.');
        }

        // ── Jika tidak pakai voucher: proses Midtrans seperti biasa ─────
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
            'notification_url' => env('APP_URL') . '/api/midtrans-callback',
            'callbacks'        => [
                'finish' => env('APP_URL') . '/orders/' . $orderId . '/struk',
                'error'  => env('APP_URL') . '/checkout_page?error=payment_failed',
                'pending'=> env('APP_URL') . '/orders/' . $orderId . '/struk',
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
        $payload  = json_decode($request->getContent(), true);
        $order_id = $payload['order_id'] ?? null;
        $status   = $payload['transaction_status'] ?? null;

        Log::info('Midtrans Callback:', ['order_id' => $order_id, 'status' => $status]);

        if (!$order_id) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        if (in_array($status, ['settlement', 'capture'])) {

            DB::table('orders')
                ->where('number', $order_id)
                ->update(['status' => 'success', 'updated_at' => now()]);

            $order = DB::table('orders')->where('number', $order_id)->first();

            if ($order) {
                // ── Update biji kopi / tandai voucher terpakai ──────────
                $orderModel = \App\Models\Order::find($order->id);
                if ($orderModel) {
                    if ($orderModel->used_voucher && $orderModel->voucher_id) {
                        // Tandai voucher sebagai terpakai
                        $voucher = Voucher::find($orderModel->voucher_id);
                        if ($voucher) {
                            CoffeeBeanService::markVoucherUsed($orderModel, $voucher);
                        }
                        // Tetap proses progress biji kopi dari produk minuman lain
                        // yang dibeli bersamaan (selain minuman gratis voucher)
                        CoffeeBeanService::processOrder($orderModel);
                    } else {
                        // Order normal → tambah progress biji kopi
                        CoffeeBeanService::processOrder($orderModel);
                    }
                }

                $this->sendNotifications(
                    $order->number,
                    $order->customer_name,
                    $order->total_price,
                    $order->order_type,
                    $order->customer_phone
                );
            }

        } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
            DB::table('orders')
                ->where('number', $order_id)
                ->update(['status' => 'cancelled', 'updated_at' => now()]);
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Konfirmasi order cash (untuk order pakai voucher yang bayar tunai)
     * Dipanggil oleh admin dari panel admin ketika pembayaran tunai sudah diterima.
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

        // Tandai voucher terpakai
        if ($order->voucher_id) {
            $orderModel = \App\Models\Order::find($orderId);
            $voucher    = Voucher::find($order->voucher_id);
            if ($orderModel && $voucher) {
                CoffeeBeanService::markVoucherUsed($orderModel, $voucher);
            }
        }

        return back()->with('success', 'Order #' . $order->number . ' dikonfirmasi lunas.');
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
            ->where('used_voucher', 0) // order voucher tidak butuh snap token
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan atau sudah tidak pending.'], 404);
        }

        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
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
            'notification_url' => env('APP_URL') . '/api/midtrans-callback',
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
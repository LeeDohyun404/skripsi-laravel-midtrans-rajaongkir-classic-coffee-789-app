<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Setting Midtrans
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // 2. Buat order ID unik
        $order_id = 'ORDER-' . time() . '-' . rand(100, 999);

        // 3. Ambil data dari form
        $phone        = $request->phone;
        $total        = (int) $request->total;
        $subtotal     = (int) $request->subtotal;
        $address      = $request->address;
        $order_type   = $request->order_type;
        $ongkir       = (int) ($request->ongkir ?? 0);
        $courierSvc   = $request->courier_service ?? null;
        $courierName  = $request->courier_name ?? null;

        // 4. Ambil cart dari session
        $cart = session()->get('cart', []);

        // 5. Simpan ke tabel orders
        $orderId = DB::table('orders')->insertGetId([
            'number'          => $order_id,
            'total_price'     => $total,
            'subtotal'        => $subtotal,
            'quantity'        => array_sum(array_column($cart, 'quantity')),
            'status'          => 'pending',
            'customer_name'   => Auth::user()->name,
            'customer_email'  => Auth::user()->email,
            'customer_phone'  => $phone,
            'order_type'      => $order_type,
            'courier_name'    => $courierName,
            'courier_service' => $courierSvc,
            'ongkir'          => $ongkir,
            'address'         => $address,
            'notes'           => $request->notes ?? null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 6. Simpan detail item ke order_items
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

        // 7. Kirim ke Midtrans
        // Build item_details dari cart
        $itemDetails = [];
        foreach ($cart as $item) {
            $itemDetails[] = [
                'id'       => 'prod-' . ($item['name'] ?? 'item'),
                'price'    => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'name'     => substr($item['name'], 0, 50), // Midtrans max 50 char
            ];
        }

        // Tambahkan ongkir sebagai item jika ada
        if ($ongkir > 0) {
            $itemDetails[] = [
                'id'       => 'ongkir',
                'price'    => $ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . $courierName . ' ' . $courierSvc . ')',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order_id,
                'gross_amount' => $total,
            ],
            'item_details'     => $itemDetails,
            'notification_url' => env('APP_URL') . '/api/midtrans-callback',
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

            // Kosongkan cart setelah order berhasil dibuat
            session()->forget('cart');

            return view('checkout', compact('snapToken', 'order_id'));
        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi email
     */
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
     * Midtrans callback
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
}
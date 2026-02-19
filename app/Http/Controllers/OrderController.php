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
        $phone      = $request->phone;
        $total      = (int) $request->total;
        $address    = $request->address;
        $order_type = $request->order_type;
        $ongkir     = (int) $request->ongkir ?? 0;
        $service    = $request->courier_service ?? '';

        // 4. Simpan ke database dengan status pending
        DB::table('orders')->insert([
            'number'         => $order_id,
            'total_price'    => $total,
            'status'         => 'pending',
            'customer_name'  => Auth::user()->name,
            'customer_email' => Auth::user()->email,
            'customer_phone' => $phone,
            'order_type'     => $order_type,
            'address'        => $address,
            'notes'          => $request->notes ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 5. Kirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id'     => $order_id,
                'gross_amount' => $total,
            ],
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
            return view('checkout', compact('snapToken', 'order_id'));
        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * ✅ KIRIM NOTIFIKASI EMAIL SAJA
     */
    private function sendNotifications($orderId, $customerName, $total, $orderType, $phone)
    {
        // Kirim Email
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
     * ✅ MIDTRANS CALLBACK - KIRIM NOTIF EMAIL SAAT PEMBAYARAN BERHASIL
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
            // Update status jadi success
            DB::table('orders')
                ->where('number', $order_id)
                ->update([
                    'status'     => 'success',
                    'updated_at' => now(),
                ]);

            // ✅ KIRIM NOTIF EMAIL (HANYA SAAT PEMBAYARAN BERHASIL)
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
            // Update status jadi cancelled (TIDAK KIRIM NOTIF)
            DB::table('orders')
                ->where('number', $order_id)
                ->update([
                    'status'     => 'cancelled',
                    'updated_at' => now(),
                ]);
        }

        return response()->json(['status' => 'OK']);
    }
}
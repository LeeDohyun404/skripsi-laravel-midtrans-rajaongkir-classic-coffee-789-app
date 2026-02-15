<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // 3. Ambil data dari form — sesuai name di checkout_page.blade.php
        $phone      = $request->phone;         // <-- fix: dari 'no_hp' ke 'phone'
        $total      = (int) $request->total;   // <-- fix: dari 'total_price' ke 'total'
        $address    = $request->address;       // <-- fix: dari 'alamat' ke 'address'
        $order_type = $request->order_type;
        $ongkir     = (int) $request->ongkir ?? 0;
        $service    = $request->courier_service ?? '';

        // 4. Simpan ke database
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

        // Tambah shipping address kalau take-away
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
                ->update([
                    'status'     => 'success',
                    'updated_at' => now(),
                ]);
        } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
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
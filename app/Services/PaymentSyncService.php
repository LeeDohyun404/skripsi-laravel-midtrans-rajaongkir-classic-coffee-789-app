<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

class PaymentSyncService
{
    /**
     * Terapkan status transaksi Midtrans ke order.
     * Aman dipanggil berkali-kali (callback Midtrans bisa dikirim ulang):
     * biji kopi hanya ditambah SEKALI, saat order pertama kali berubah ke success.
     */
    public static function apply(Order $order, ?string $transactionStatus, ?string $fraudStatus = null): void
    {
        $paid = in_array($transactionStatus, ['settlement', 'capture'], true)
            && ($transactionStatus !== 'capture' || in_array($fraudStatus, [null, 'accept'], true));

        if ($paid) {
            $justPaid = DB::transaction(function () use ($order) {
                $row = Order::where('id', $order->id)->lockForUpdate()->first();
                if (!$row || in_array($row->status, ['success', 'completed'], true)) {
                    return false; // sudah diproses sebelumnya
                }
                $row->update(['status' => 'success']);
                return true;
            });

            if ($justPaid) {
                $order->refresh();

                if ($order->used_voucher && $order->voucher_id) {
                    $voucher = Voucher::find($order->voucher_id);
                    if ($voucher) {
                        CoffeeBeanService::markVoucherUsed($order, $voucher);
                    }
                }

                CoffeeBeanService::processOrder($order);
                self::notifyAdmin($order);
            }
            return;
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true) && $order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
        }
    }

    /**
     * Tanya status ke Midtrans langsung (tidak bergantung pada callback).
     * @return bool true kalau status order berubah
     */
    public static function syncFromMidtrans(Order $order): bool
    {
        try {
            Config::$serverKey    = config('services.midtrans.serverKey');
            Config::$isProduction = config('services.midtrans.isProduction');

            $res = Transaction::status($order->number);
            $res = is_object($res) ? $res : (object) $res;

            $before = $order->status;
            self::apply($order, $res->transaction_status ?? null, $res->fraud_status ?? null);

            return $order->fresh()->status !== $before;
        } catch (\Throwable $e) {
            Log::warning('Sync status Midtrans gagal: ' . $e->getMessage(), ['order' => $order->number]);
            return false;
        }
    }

    private static function notifyAdmin(Order $order): void
    {
        try {
            \Illuminate\Support\Facades\Notification::route('mail', env('ADMIN_EMAIL', 'admin@classic789.com'))
                ->notify(new \App\Notifications\NewOrderNotification(
                    $order->number, $order->customer_name, $order->total_price, $order->order_type, $order->customer_phone
                ));
        } catch (\Throwable $e) {
            Log::error('Email notifikasi order gagal: ' . $e->getMessage());
        }
    }
}

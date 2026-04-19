<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class CoffeeBeanService
{
    /**
     * Proses penambahan biji kopi setelah order sukses.
     * Dipanggil dari OrderController saat status order berubah jadi success/settlement/capture.
     *
     * @param  mixed  $order  — bisa Eloquent model atau stdClass dari DB::table()
     * @return void
     */
    public static function processOrder($order): void
    {
        // Cari user berdasarkan email customer
        $user = User::where('email', $order->customer_email)->first();
        if (! $user) {
            return;
        }

        // Hitung total qty minuman di order ini
        // Jika order pakai voucher, kurangi 1 qty (minuman gratis tidak dihitung)
        $drinkQty = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $order->id)
            ->whereRaw('LOWER(products.category) = ?', ['minuman'])
            ->sum('order_items.quantity');

        // Minuman yang digratiskan voucher tidak dihitung progress
        if ($order->used_voucher && $drinkQty > 0) {
            $drinkQty -= 1;
        }

        if ($drinkQty <= 0) {
            return;
        }

        // Tambah progress
        $currentProgress = $user->coffee_bean_progress ?? 0;
        $newProgress     = $currentProgress + $drinkQty;

        // Hitung voucher yang perlu dibuat
        $vouchersToIssue = (int) floor($newProgress / 10);
        $remainder       = $newProgress % 10;

        // Update progress user
        $user->update(['coffee_bean_progress' => $remainder]);

        // Issue voucher untuk setiap kelipatan 10
        for ($i = 0; $i < $vouchersToIssue; $i++) {
            // Max 1 voucher aktif sekaligus
            $hasActive = Voucher::activeFor($user->id)->exists();
            if ($hasActive) {
                break;
            }

            Voucher::create([
                'user_id'    => $user->id,
                'code'       => Voucher::generateCode(),
                'type'       => 'free_drink',
                'status'     => 'active',
                'expires_at' => now()->addDays(7),
            ]);
        }
    }

    /**
     * Tandai voucher sebagai terpakai setelah order dengan voucher sukses.
     *
     * @param  mixed    $order
     * @param  Voucher  $voucher
     * @return void
     */
    public static function markVoucherUsed($order, Voucher $voucher): void
    {
        $voucher->update([
            'status'        => 'used',
            'used_at'       => now(),
            'used_order_id' => $order->id,
        ]);

        // Hapus session voucher
        session()->forget(['voucher_active', 'voucher_id', 'voucher_code']);
    }
}
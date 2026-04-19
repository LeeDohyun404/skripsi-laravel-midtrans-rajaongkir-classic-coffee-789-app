<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Aktifkan voucher ke session (tombol "Gunakan")
     * Voucher disimpan di session supaya bisa dipakai di halaman keranjang/checkout.
     */
    public function activate(Request $request)
    {
        $request->validate(['voucher_id' => 'required|integer']);

        $voucher = Voucher::where('id', $request->voucher_id)
                          ->where('user_id', Auth::id())
                          ->where('status', 'active')
                          ->where('expires_at', '>', now())
                          ->firstOrFail();

        // Simpan id voucher aktif ke session
        session([
            'voucher_active'    => true,
            'voucher_id'        => $voucher->id,
            'voucher_code'      => $voucher->code,
        ]);

        return redirect()->route('profile')
                         ->with('success', '✅ Voucher diaktifkan! Lanjutkan belanja dan voucher akan otomatis diterapkan.');
    }

    /**
     * Batalkan penggunaan voucher (tombol "Batal")
     */
    public function deactivate()
    {
        session()->forget(['voucher_active', 'voucher_id', 'voucher_code']);

        return redirect()->route('profile')
                         ->with('success', 'Voucher dinonaktifkan dari keranjang.');
    }

    /**
     * Terapkan voucher saat checkout — dipanggil dari OrderController
     * Mengembalikan harga minuman termurah di keranjang sebagai diskon.
     *
     * @param  array  $cartItems  — array item keranjang, tiap item punya: price, quantity, category
     * @return array ['discount' => int, 'voucher' => Voucher|null]
     */
    public static function applyVoucherToCart(array $cartItems): array
    {
        if (! session('voucher_active') || ! session('voucher_id')) {
            return ['discount' => 0, 'voucher' => null];
        }

        $voucher = Voucher::where('id', session('voucher_id'))
                          ->where('status', 'active')
                          ->where('expires_at', '>', now())
                          ->first();

        if (! $voucher) {
            session()->forget(['voucher_active', 'voucher_id', 'voucher_code']);
            return ['discount' => 0, 'voucher' => null];
        }

        // Cari minuman dengan harga terendah di keranjang untuk digratiskan
        $drinkItems = array_filter($cartItems, fn($item) =>
            isset($item['category']) && strtolower($item['category']) === 'minuman'
        );

        if (empty($drinkItems)) {
            return ['discount' => 0, 'voucher' => $voucher];
        }

        // Ambil harga unit terendah dari minuman
        $cheapest = collect($drinkItems)->min('price');

        return ['discount' => (int) $cheapest, 'voucher' => $voucher];
    }
}
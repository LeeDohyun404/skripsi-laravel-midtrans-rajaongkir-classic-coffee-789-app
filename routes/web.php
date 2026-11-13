<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Auth\KaryawanLoginController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AktivasiKaryawanController;
use App\Http\Controllers\KaryawanAuthController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\StaffPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ProductImageController;

// =============================================
// ✅ API RAJAONGKIR — WAJIB PALING ATAS
// =============================================
Route::get('/api/search-destination', [RajaOngkirController::class, 'searchDestination']);
Route::post('/api/ongkir', [RajaOngkirController::class, 'getCost']);

// Gambar produk: fallback ke database kalau file tidak ada di disk
Route::get('/images/{filename}', [ProductImageController::class, 'show'])->where('filename', '[A-Za-z0-9._-]+');
Route::get('/foto-produk', [ProductImageController::class, 'showQuery']);
Route::get('/storage/avatars/{filename}', [ProductImageController::class, 'show'])->where('filename', '[A-Za-z0-9._-]+');
Route::get('/foto-avatar/{filename}', [ProductImageController::class, 'show'])->where('filename', '[A-Za-z0-9._-]+');

// =============================================
// HALAMAN UTAMA
// =============================================
Route::get('/', function () {
    $products = Schema::hasTable('products') ? DB::table('products')->limit(3)->get() : [];
    return view('index', ['products' => $products, 'slides' => [], 'diskonGlobal' => '0']);
});

// =============================================
// PRODUK
// =============================================
Route::get('/produk', function () {
    if (!Schema::hasTable('products')) return "Tabel products tidak ditemukan di database!";
    $products = DB::table('products')->get();
    return view('produk', ['products' => $products, 'kategori' => null, 'subkategori' => null]);
});

Route::get('/produk/{kategori}/{subkategori?}', function ($kategori, $subkategori = null) {
    if (!Schema::hasTable('products')) return "Tabel products tidak ditemukan!";
    $query = DB::table('products');
    if (Schema::hasColumn('products', 'category')) {
        $katCari = str_replace('-', ' ', $kategori);
        $query->where('category', 'like', "%$katCari%");
    }
    if ($subkategori && Schema::hasColumn('products', 'subcategory')) {
        $subCari = str_replace('-', ' ', $subkategori);
        $query->where(function ($q) use ($subkategori, $subCari) {
            $q->where('subcategory', 'like', "%$subkategori%")
              ->orWhere('subcategory', 'like', "%$subCari%");
        });
    }
    $products = $query->get();
    return view('produk', ['products' => $products, 'kategori' => $kategori, 'subkategori' => $subkategori]);
});

// =============================================
// HALAMAN STATIS
// =============================================
Route::get('/about', function () { return view('about'); });
Route::get('/service', function () { return view('service'); });

// =============================================
// AUTH PELANGGAN (web guard)
// =============================================
Auth::routes(['verify' => true]);

Route::get('/home', function () {
    return redirect('/produk');
});

// =============================================
// CHECKOUT
// =============================================
Route::get('/checkout_page', [RajaOngkirController::class, 'checkout'])->middleware(['auth', 'verified']);
Route::post('/payment/process', [OrderController::class, 'checkout'])->name('payment.process')->middleware(['auth', 'verified']);
Route::post('/payment/cash',    [OrderController::class, 'checkout'])->name('payment.cash')->middleware(['auth', 'verified']);

// =============================================
// CART + PROFILE PELANGGAN (auth web)
// =============================================
Route::middleware(['auth'])->group(function () {

    // Keranjang
    Route::get('/keranjang', function () {
        return view('keranjang');
    });

    Route::post('/cart/add', function (Request $request) {
        $cart = session()->get('cart', []);
        $id = $request->id;
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name"     => $request->name,
                "quantity" => 1,
                "price"    => $request->price,
                "image"    => $request->image,
                "category" => $request->category ?? DB::table('products')->where('id', $id)->value('category'),
            ];
        }
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produk masuk keranjang!');
    });

    Route::get('/cart/update/{id}/{type}', function ($id, $type) {
        $cart = session()->get('cart');
        if ($type == 'plus') {
            $cart[$id]['quantity']++;
        } else {
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                unset($cart[$id]);
            }
        }
        session()->put('cart', $cart);
        return redirect()->back();
    });

    // Profile pelanggan
    Route::get('/profile',            [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile/name',     [ProfileController::class, 'updateName'])->name('profile.name');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar',    [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Voucher
    Route::post('/voucher/activate',   [VoucherController::class, 'activate'])->name('voucher.activate');
    Route::post('/voucher/deactivate', [VoucherController::class, 'deactivate'])->name('voucher.deactivate');

    // Struk & refresh token Midtrans
    Route::get('/orders/{id}/struk',          [OrderController::class, 'struk'])->name('orders.struk');
    Route::post('/orders/{id}/refresh-token', [OrderController::class, 'refreshToken'])->name('orders.refresh-token');

    // ── Konfirmasi pesanan selesai oleh pelanggan ──
    Route::post('/orders/{id}/selesai', [OrderController::class, 'konfirmasiSelesai'])->name('orders.selesai');

    // ── Pengaduan pelanggan ──
    Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');

});

// =============================================
// GOOGLE AUTH
// =============================================
Route::get('auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback']);

// =============================================
// AKTIVASI KARYAWAN (publik)
// =============================================
Route::prefix('aktivasi')->name('aktivasi.')->group(function () {
    Route::get('/invalid', [AktivasiKaryawanController::class, 'invalid'])->name('invalid');
    Route::get('/{token}', [AktivasiKaryawanController::class, 'tampilForm'])->name('form');
    Route::post('/{token}', [AktivasiKaryawanController::class, 'proses'])->name('proses');
});

// =============================================
// AUTH KARYAWAN — Login / Logout
// =============================================
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [KaryawanAuthController::class, 'showLogin'])
        ->name('login')
        ->middleware('guest:karyawan');
    Route::post('/login', [KaryawanAuthController::class, 'login'])
        ->name('login.post')
        ->middleware('guest:karyawan');
    Route::post('/logout', [KaryawanAuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth.karyawan');
});

Route::get('/staff/forgot-password', [StaffPasswordController::class, 'showForgotForm'])->name('staff.password.request');
Route::post('/staff/forgot-password', [StaffPasswordController::class, 'sendResetLink'])->name('staff.password.email');
Route::get('/staff/reset-password/{token}', [StaffPasswordController::class, 'showResetForm'])->name('staff.password.reset');
Route::post('/staff/reset-password', [StaffPasswordController::class, 'resetPassword'])->name('staff.password.update');

// =============================================
// ADMIN LAMA (AdminController — guard web/admin_auth)
// =============================================
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin_auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/history', [AdminController::class, 'history']);
    Route::get('/admin/pelanggan', [AdminController::class, 'customers']);

    Route::get('/admin/tambah', function () {
        return view('admin.tambah');
    });

    Route::post('/admin/simpan', function (Request $request) {
        $name_image = 'default.jpg';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $name_image);
        }
        DB::table('products')->insert([
            'name'       => $request->name,
            'price'      => $request->price,
            'category'   => $request->category,
            'image_url'  => $name_image,
            'created_at' => now(),
        ]);
        return redirect('/admin')->with('success', 'Produk berhasil ditambah!');
    });

    Route::post('/admin/update', function (Request $request) {
        $data = [
            'name'     => $request->name,
            'price'    => $request->price,
            'category' => $request->category,
        ];
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $name_image);
            $data['image_url'] = $name_image;
        }
        DB::table('products')->where('id', $request->id)->update($data);
        return redirect('/admin')->with('success', 'Produk Berhasil Diperbarui!');
    });

    Route::get('/admin/hapus/{id}', function ($id) {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('products')->where('id', $id)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect('/admin')->with('success', 'Produk Berhasil Dihapus!');
        } catch (\Exception $e) {
            return redirect('/admin')->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    });
});

// =============================================
// ADMIN KARYAWAN — guard karyawan, role admin
// =============================================
Route::prefix('admin')->name('admin.')->middleware(['auth.karyawan', 'role.karyawan:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/profil/nama', [ProfilController::class, 'gantiNama'])->name('profil.nama');
    Route::post('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');

    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::get('/tambah', [KaryawanController::class, 'create'])->name('tambah');
        Route::post('/tambah', [KaryawanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KaryawanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KaryawanController::class, 'update'])->name('update');
        Route::delete('/{id}', [KaryawanController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/resend', [KaryawanController::class, 'resendUndangan'])->name('resend');
    });

    Route::get('/pelanggan', [\App\Http\Controllers\Admin\PelangganController::class, 'index'])->name('pelanggan');
    Route::delete('/pelanggan/{id}/hapus', [\App\Http\Controllers\Admin\PelangganController::class, 'destroy'])->name('pelanggan.hapus');
    Route::get('/history', [AdminController::class, 'history'])->name('history');
    Route::get('/stok', [AdminController::class, 'stok'])->name('stok');
});

// =============================================
// OPERATOR — guard karyawan, role operator
// =============================================
Route::prefix('operator')->name('operator.')->middleware(['auth.karyawan', 'role.karyawan:operator'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\OperatorController::class, 'dashboard'])->name('dashboard');
    Route::get('/stok', [App\Http\Controllers\OperatorController::class, 'stok'])->name('stok');
    Route::post('/produk/simpan', [App\Http\Controllers\OperatorController::class, 'storeProduk'])->name('produk.store');
    Route::put('/produk/update', [App\Http\Controllers\OperatorController::class, 'updateProduk'])->name('produk.update');
    Route::post('/stok/update', [App\Http\Controllers\OperatorController::class, 'updateStok'])->name('stok.update');
    Route::delete('/produk/{id}', [App\Http\Controllers\OperatorController::class, 'destroyProduk'])->name('produk.hapus');
    Route::post('/profil/nama', [ProfilController::class, 'gantiNama'])->name('profil.nama');
    Route::post('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');

    // ── TRACKING (baru) ──
    Route::get('/tracking', [App\Http\Controllers\OperatorController::class, 'tracking'])->name('tracking');
    Route::post('/tracking/{id}/update', [App\Http\Controllers\OperatorController::class, 'updateTracking'])->name('tracking.update');

    // ── PENGADUAN (baru) ──
    Route::get('/pengaduan', [App\Http\Controllers\OperatorController::class, 'pengaduan'])->name('pengaduan');
    Route::post('/pengaduan/{id}/update', [App\Http\Controllers\OperatorController::class, 'updatePengaduan'])->name('pengaduan.update');
});

// =============================================
// KASIR — guard karyawan, role kasir
// =============================================
Route::prefix('kasir')->name('kasir.')->middleware(['auth.karyawan', 'role.karyawan:kasir'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [App\Http\Controllers\KasirController::class, 'transaksi'])->name('transaksi');
    Route::put('/pesanan/{id}', [App\Http\Controllers\KasirController::class, 'updatePesanan'])->name('pesanan.update');
    Route::get('/detail-pelanggan', [KasirController::class, 'detailPelanggan'])->name('detail.pelanggan');
    Route::get('/detail-pesanan/{id}', [KasirController::class, 'detailPesanan'])->name('detail.pesanan');
    Route::post('/transaksi/store', [App\Http\Controllers\KasirController::class, 'storeTransaksi'])->name('transaksi.store');
    Route::post('/profil/nama', [ProfilController::class, 'gantiNama'])->name('profil.nama');
    Route::post('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
    Route::get('/laporan-detail', [App\Http\Controllers\KasirController::class, 'laporanDetail'])->name('laporan.detail');
});
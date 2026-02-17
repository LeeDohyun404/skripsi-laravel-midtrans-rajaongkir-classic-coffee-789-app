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

// =============================================
// ✅ API RAJAONGKIR — WAJIB PALING ATAS
// =============================================
Route::get('/api/search-destination', [RajaOngkirController::class, 'searchDestination']);
Route::post('/api/ongkir', [RajaOngkirController::class, 'getCost']);

// =============================================
// MIDTRANS CALLBACK
// =============================================
Route::post('/api/midtrans-callback', [OrderController::class, 'notificationHandler']);

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
// AUTH
// =============================================
Auth::routes();

Route::get('/home', function () {
    return redirect('/produk');
});

// =============================================
// CHECKOUT
// =============================================
Route::get('/checkout_page', [RajaOngkirController::class, 'checkout'])->middleware('auth');
Route::post('/payment/process', [OrderController::class, 'checkout'])->name('payment.process');

// =============================================
// CART
// =============================================
Route::middleware(['auth'])->group(function () {

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
                "image"    => $request->image
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
});

// =============================================
// ADMIN LOGIN
// =============================================
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// =============================================
// ADMIN DASHBOARD
// =============================================
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

Route::get('auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback']);
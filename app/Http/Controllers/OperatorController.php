<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OperatorController extends Controller
{
    public function dashboard()
    {
        $totalProduk = DB::table('products')->count();
        $stokRendah = DB::table('products')->whereBetween('stock', [1, 10])->count();
        $stokAman = DB::table('products')->where('stock', '>', 10)->count();
        $stokHampirHabis = DB::table('products')
            ->select('name as nama_produk', 'category as kategori', 'stock as stok')
            ->whereBetween('stock', [1, 10])
            ->orderBy('stock')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->satuan = 'pcs';
                return $item;
            });

        return view('operator.dashboard', compact(
            'totalProduk',
            'stokRendah',
            'stokAman',
            'stokHampirHabis'
        ));
    }

    public function stok(Request $request)
    {
        $query = DB::table('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        if ($request->filled('stok_filter')) {
            match ($request->stok_filter) {
                'habis'  => $query->where('stock', '<=', 0),
                'rendah' => $query->whereBetween('stock', [1, 10]),
                'aman'   => $query->where('stock', '>', 10),
                default  => null,
            };
        }

        $products     = $query->paginate(10);
        $kategoriList = DB::table('products')->distinct()->pluck('category')->filter();

        $totalProduk = DB::table('products')->count();
        $stokHabis   = DB::table('products')->where('stock', '<=', 0)->count();
        $stokRendah  = DB::table('products')->whereBetween('stock', [1, 10])->count();
        $stokAman    = DB::table('products')->where('stock', '>', 10)->count();

        return view('operator.stok', compact(
            'products', 'kategoriList',
            'totalProduk', 'stokHabis', 'stokRendah', 'stokAman'
        ));
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'name'  => 'required|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $image = 'default.jpg';
        if ($request->hasFile('image')) {
            $img   = $request->file('image');
            $image = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('images'), $image);
        }

        DB::table('products')->insert([
            'name'       => $request->name,
            'price'      => $request->price,
            'category'   => $request->category,
            'stock'      => $request->stock ?? 0,
            'image_url'  => $image,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function updateProduk(Request $request)
    {
        $request->validate([
            'id'    => 'required',
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        DB::table('products')->where('id', $request->id)->update([
            'name'       => $request->name,
            'price'      => $request->price,
            'category'   => $request->category,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function updateStok(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'stock'      => 'required|integer|min:0',
        ]);

        DB::table('products')->where('id', $request->product_id)->update([
            'stock'      => $request->stock,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Stok berhasil diperbarui!');
    }

    public function destroyProduk($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return back()->with('success', 'Produk berhasil dihapus!');
    }
}

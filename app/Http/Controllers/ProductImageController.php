<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ProductImageController extends Controller
{
    /** Buat tabel otomatis kalau belum ada (tidak perlu SQL manual). */
    private static function pastikanTabel(): void
    {
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->unique();
                $table->string('mime', 100);
                $table->longText('data');
                $table->timestamps();
            });
        }
    }

    /** Simpan file upload ke database, kembalikan nama file untuk kolom image_url. */
    public static function simpan($file): string
    {
        self::pastikanTabel();

        $ext  = strtolower($file->extension() ?: 'jpg');
        $name = time() . '-' . bin2hex(random_bytes(3)) . '.' . $ext;

        DB::table('product_images')->insert([
            'filename'   => $name,
            'mime'       => $file->getMimeType() ?: 'image/jpeg',
            'data'       => base64_encode(file_get_contents($file->getRealPath())),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $name;
    }

    /** Simpan binary data langsung (dari canvas/base64 JS). */
    public static function simpanRaw(string $filename, string $mime, string $binaryData): void
    {
        self::pastikanTabel();

        DB::table('product_images')->insert([
            'filename'   => $filename,
            'mime'       => $mime,
            'data'       => base64_encode($binaryData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** URL tanpa ekstensi (?f=nama.jpg) supaya tidak dicegat server statis. */
    public function showQuery(\Illuminate\Http\Request $request)
    {
        $f = (string) $request->query('f', '');
        $filename = basename($f);
        return $this->show($filename);
    }

    /** Dipakai kalau file tidak ada di public/images (mis. hilang setelah deploy). */
    public function show(string $rawFilename)
    {
        $filename = basename($rawFilename);

        if (empty($filename)) {
            abort(404);
        }

        // 1. Cek disk public/images
        $paths = [
            public_path('images/' . $filename),
            public_path('images/complaints/' . $filename),
            storage_path('app/public/complaints/' . $filename),
            storage_path('app/public/avatars/' . $filename),
            storage_path('app/public/' . $filename),
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                // Auto-sync ke DB jika belum ada
                try {
                    $exists = DB::table('product_images')->where('filename', $filename)->exists();
                    if (!$exists) {
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        $mime = match($ext) {
                            'png'   => 'image/png',
                            'webp'  => 'image/webp',
                            'gif'   => 'image/gif',
                            default => 'image/jpeg',
                        };
                        DB::table('product_images')->insert([
                            'filename'   => $filename,
                            'mime'       => $mime,
                            'data'       => base64_encode(file_get_contents($path)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (\Throwable $e) {}

                return response()->file($path);
            }
        }

        // 2. Ambil dari DB — langsung query tanpa Schema::hasTable (lebih andal di Wasmer WASM)
        try {
            $img = DB::table('product_images')
                ->where('filename', $filename)
                ->first();

            if ($img && !empty($img->data)) {
                $decoded = base64_decode($img->data, true);
                if ($decoded && strlen($decoded) > 10) {
                    return response($decoded, 200, [
                        'Content-Type'  => $img->mime ?: 'image/jpeg',
                        'Cache-Control' => 'public, max-age=86400',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // DB error — fall through to fallback
        }

        // Jika gambar tidak ditemukan (misal file lama yang sempat hilang di Wasmer),
        // kembalikan SVG placeholder resmi yang bersih (bukan gambar acak/anime).
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260" viewBox="0 0 400 260">
            <rect width="400" height="260" rx="12" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
            <text x="50%" y="42%" dominant-baseline="middle" text-anchor="middle" font-size="34">📷</text>
            <text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" font-family="-apple-system, sans-serif" font-size="13" font-weight="700" fill="#475569">Foto Bukti Tidak Tersedia</text>
            <text x="50%" y="72%" dominant-baseline="middle" text-anchor="middle" font-family="-apple-system, sans-serif" font-size="11" fill="#94a3b8">File arsip server sebelumnya hilang</text>
        </svg>';
        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }
}
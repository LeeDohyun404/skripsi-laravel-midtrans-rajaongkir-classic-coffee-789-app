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

    /** URL tanpa ekstensi (?f=nama.jpg) supaya tidak dicegat server statis. */
    public function showQuery(\Illuminate\Http\Request $request)
    {
        return $this->show((string) $request->query('f', ''));
    }

    /** Dipakai kalau file tidak ada di public/images (mis. hilang setelah deploy). */
    public function show(string $filename)
    {
        $path = public_path('images/' . $filename);
        if (is_file($path)) {
            return response()->file($path);
        }

        if (Schema::hasTable('product_images')) {
            $img = DB::table('product_images')->where('filename', $filename)->first();
            if ($img) {
                return response(base64_decode($img->data), 200, [
                    'Content-Type'  => $img->mime,
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        abort(404);
    }
}
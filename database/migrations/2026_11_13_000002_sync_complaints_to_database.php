<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan tabel product_images ada
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->unique();
                $table->string('mime', 100);
                $table->longText('data');
                $table->timestamps();
            });
        }

        // 2. Scan file fisik di storage/app/public/complaints
        $dir = storage_path('app/public/complaints');
        if (is_dir($dir)) {
            $files = glob($dir . '/*.*');
            foreach ($files as $filePath) {
                if (is_file($filePath)) {
                    $filename = basename($filePath);
                    try {
                        $exists = DB::table('product_images')->where('filename', $filename)->exists();
                        if (!$exists) {
                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $mime = match($ext) {
                                'png'   => 'image/png',
                                'webp'  => 'image/webp',
                                'gif'   => 'image/gif',
                                default => 'image/jpeg',
                            };
                            $data = base64_encode(file_get_contents($filePath));
                            DB::table('product_images')->insert([
                                'filename'   => $filename,
                                'mime'       => $mime,
                                'data'       => $data,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } catch (\Throwable $e) {
                        // ignore duplicate or error
                    }
                }
            }
        }

        // 3. Fallback: pastikan file foto bukti pengaduan saat ini tersimpan jika belum ada
        $fallbackFilename = 'wdttruPf7SR91Q7b6W0CUDQNlEmEazqaLjsqJ3b1.jpg';
        try {
            $exists = DB::table('product_images')->where('filename', $fallbackFilename)->exists();
            if (!$exists) {
                $localPath = storage_path('app/public/complaints/' . $fallbackFilename);
                if (is_file($localPath)) {
                    DB::table('product_images')->insert([
                        'filename'   => $fallbackFilename,
                        'mime'       => 'image/jpeg',
                        'data'       => base64_encode(file_get_contents($localPath)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};

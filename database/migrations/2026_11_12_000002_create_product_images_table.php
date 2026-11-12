<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Disk Wasmer tidak permanen, jadi foto produk disimpan di database.
    public function up(): void
    {
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->unique();
                $table->string('mime', 100);
                $table->longText('data'); // isi gambar (base64)
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};

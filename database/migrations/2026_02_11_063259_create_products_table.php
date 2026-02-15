<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('price');
    $table->string('category')->nullable();
    $table->string('image_url')->default('default.jpg');
    // TAMBAHKAN 3 BARIS INI BIAR GAK ERROR:
    $table->integer('discount_percentage')->default(0);
    $table->decimal('average_rating', 3, 1)->default(0);
    $table->integer('review_count')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Aman dijalankan walau kolom sudah ada (database Wasmer bisa saja hasil import)
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'coffee_bean_progress')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('coffee_bean_progress')->default(0);
            });
        }
    }

    public function down(): void
    {
        //
    }
};

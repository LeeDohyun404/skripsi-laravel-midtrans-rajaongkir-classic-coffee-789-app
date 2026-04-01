<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel karyawan untuk menyimpan data Admin, Operator, dan Kasir.
     *
     * Peran yang tersedia:
     *  - admin    : Super User, akses penuh ke seluruh sistem
     *  - operator : Back Office / Warehouse, kelola produk & stok
     *  - kasir    : Front Liner, kelola transaksi pelanggan
     */
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            // ── Primary Key ───────────────────────────────────────────
            $table->id();

            // ── Identitas ─────────────────────────────────────────────
            $table->string('nama',          100)->comment('Nama lengkap karyawan');
            $table->string('username',       50)->unique()->comment('Username untuk login');
            $table->string('email',         150)->unique()->nullable()->comment('Email karyawan');
            $table->string('password')         ->comment('Password ter-hash (bcrypt)');
            $table->string('no_hp',          20)->nullable()->comment('Nomor handphone');
            $table->string('foto',          255)->nullable()->comment('Path foto profil');

            // ── Peran & Status ────────────────────────────────────────
            $table->enum('peran', ['admin', 'operator', 'kasir'])
                  ->default('kasir')
                  ->comment('Peran: admin = Super User | operator = Back Office | kasir = Front Liner');

            $table->boolean('is_aktif')
                  ->default(true)
                  ->comment('Status aktif akun karyawan');

            // ── Shift (opsional, relevan untuk kasir) ─────────────────
            $table->string('shift', 20)->nullable()->comment('Shift kerja: pagi / siang / malam');

            // ── Keamanan / Session ────────────────────────────────────
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('last_login_at')->nullable()->comment('Waktu login terakhir');

            // ── Audit ─────────────────────────────────────────────────
            $table->timestamps();                    // created_at & updated_at
            $table->softDeletes();                   // deleted_at (hapus non-permanen)

            // ── Indexes ───────────────────────────────────────────────
            $table->index('peran');
            $table->index('is_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};

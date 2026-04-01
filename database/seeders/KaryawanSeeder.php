<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Isi data awal: 1 admin, 1 operator, 1 kasir.
     * Ganti password sebelum deploy ke production!
     */
    public function run(): void
    {
        $data = [
            [
                'nama'     => 'Super Admin',
                'username' => 'admin',
                'email'    => 'admin@toko.com',
                'password' => Hash::make('Admin@1234'),
                'peran'    => 'admin',
                'is_aktif' => true,
            ],
            [
                'nama'     => 'Operator Gudang',
                'username' => 'operator',
                'email'    => 'operator@toko.com',
                'password' => Hash::make('Operator@1234'),
                'peran'    => 'operator',
                'is_aktif' => true,
                'shift'    => 'pagi',
            ],
            [
                'nama'     => 'Kasir Utama',
                'username' => 'kasir',
                'email'    => 'kasir@toko.com',
                'password' => Hash::make('Kasir@1234'),
                'peran'    => 'kasir',
                'is_aktif' => true,
                'shift'    => 'pagi',
            ],
        ];

        foreach ($data as $row) {
            Karyawan::updateOrCreate(
                ['username' => $row['username']],
                $row
            );
        }

        $this->command->info('Seeder KaryawanSeeder berhasil dijalankan.');
    }
}

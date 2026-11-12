<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    | web       → pelanggan (tabel users)
    | karyawan  → semua staff (admin, operator, kasir) — tabel karyawan
    | staff     → ALIAS sementara untuk karyawan (hapus setelah semua kode diganti)
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'karyawan' => [
            'driver' => 'session',
            'provider' => 'karyawan',
        ],

        'staff' => [
            'driver' => 'session',
            'provider' => 'karyawan',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'karyawan' => [
            'driver' => 'eloquent',
            'model' => App\Models\Karyawan::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'karyawan' => [
            'provider' => 'karyawan',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
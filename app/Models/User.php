<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'coffee_bean_progress', // ← tambah ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Accessor untuk avatar:
     * Pastikan avatar lokal SELALU menggunakan URL tanpa ekstensi di path
     * misal: /foto-avatar?f=av-xxxx.jpg
     * agar TIDAK dicegat oleh server statis WebAssembly/Wasmer.
     */
    public function getAvatarAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Jika Google avatar eksternal
        if (str_contains($value, 'googleusercontent.com') || str_contains($value, 'lh3.google')) {
            return $value;
        }

        // Ambil nama file dari URL atau path
        $filename = basename(parse_url($value, PHP_URL_PATH));
        if (!empty(parse_url($value, PHP_URL_QUERY))) {
            parse_str(parse_url($value, PHP_URL_QUERY), $query);
            if (!empty($query['f'])) {
                $filename = basename($query['f']);
            }
        }

        if (empty($filename)) {
            return $value;
        }

        return url('/foto-avatar') . '?f=' . $filename;
    }
}
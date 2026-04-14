<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'karyawan';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'no_hp',
        'foto',
        'peran',
        'is_aktif',
        'shift',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_aktif'      => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ── Helper Peran ──────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->peran === 'operator';
    }

    public function isKasir(): bool
    {
        return $this->peran === 'kasir';
    }

    // ── Helper Status ─────────────────────────────────────────────────

    /**
     * Cek apakah karyawan sudah aktif (is_aktif = true & password sudah diset).
     */
    public function sudahAktif(): bool
    {
        return (bool) $this->is_aktif && !empty($this->password);
    }

    /**
     * Cek apakah undangan masih valid (belum aktif tapi password belum diset).
     * Karena tabel tidak punya kolom aktivasi_token/expired_at,
     * "diundang" = belum aktif tapi sudah punya email.
     */
    public function tokenMasihValid(): bool
    {
        return !(bool) $this->is_aktif && !empty($this->email) && empty($this->password);
    }

    // ── Scope ─────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopePeran($query, string $peran)
    {
        return $query->where('peran', $peran);
    }

    // ── Label Peran (untuk tampilan) ──────────────────────────────────

    public function getLabelPeranAttribute(): string
    {
        return match ($this->peran) {
            'admin'    => 'Admin (Super User)',
            'operator' => 'Operator (Back Office)',
            'kasir'    => 'Kasir (Front Liner)',
            default    => ucfirst($this->peran),
        };
    }

    public function getBadgePeranAttribute(): string
    {
        return match ($this->peran) {
            'admin'    => 'badge-danger',
            'operator' => 'badge-warning',
            'kasir'    => 'badge-success',
            default    => 'badge-secondary',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'status',
        'used_at',
        'used_order_id',
        'expires_at',
    ];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usedOrder()
    {
        return $this->belongsTo(Order::class, 'used_order_id');
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    /** Voucher aktif dan belum expired */
    public function scopeActiveFor($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->where('status', 'active')
                     ->where('expires_at', '>', now());
    }

    /** Voucher yang sudah dipakai */
    public function scopeUsedBy($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->where('status', 'used')
                     ->orderByDesc('used_at');
    }

    // ──────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ! $this->isExpired();
    }

    /**
     * Generate kode voucher unik
     */
    public static function generateCode(): string
    {
        do {
            $code = 'CC789-' . strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'nama', 'no_hp', 'alamat',
        'total_price', 'order_type', 'status', 'snap_token',
        'number', 'quantity', 'customer_name', 'customer_email',
        'customer_phone', 'courier_name', 'courier_service',
        'ongkir', 'subtotal', 'notes', 'address',
    ];

    // ✅ Relasi ke order_items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
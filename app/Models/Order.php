<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // PINDAHKAN KE SINI, BUKAN DI CONTROLLER!
    protected $fillable = [
        'user_id', 'nama', 'no_hp', 'alamat', 
        'total_price', 'order_type', 'status', 'snap_token'
    ];
    
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id','nama','no_hp','alamat','total_price','order_type','status','snap_token',
        'number','quantity','customer_name','customer_email','customer_phone',
        'courier_name','courier_service','ongkir','subtotal','notes','address',
        'used_voucher','voucher_id','voucher_discount','exclude_from_stats',
        'tracking_status','estimasi_selesai',
    ];
    protected $casts = ['estimasi_selesai' => 'date'];

    public function items()      { return $this->hasMany(OrderItem::class); }
    public function trackings()  { return $this->hasMany(OrderTracking::class)->orderBy('created_at','asc'); }
    public function complaints() { return $this->hasMany(OrderComplaint::class); }
    public function latestTracking() { return $this->hasOne(OrderTracking::class)->latestOfMany(); }
    public function isTakeAway(): bool { return $this->order_type === 'take-away'; }
    public function bisaDikonfirmasiSelesai(): bool { return $this->tracking_status === 'dikirim'; }
}
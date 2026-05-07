<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $fillable = ['order_id','status','keterangan','updated_by'];

    public static array $statusConfig = [
        'dibuat'              => ['label'=>'Pesanan Dibuat',          'icon'=>'fa-clipboard-check', 'desc'=>'Pesanan berhasil masuk ke sistem.'],
        'dikemas'             => ['label'=>'Pesanan Dikemas',         'icon'=>'fa-box',              'desc'=>'Pesanan sedang disiapkan dan dikemas.'],
        'dikirim_ke_pengirim' => ['label'=>'Diserahkan ke Pengirim',  'icon'=>'fa-handshake',        'desc'=>'Paket sudah diserahkan ke pihak kurir.'],
        'dikirim'             => ['label'=>'Sedang Dikirim',          'icon'=>'fa-truck',            'desc'=>'Paket sedang dalam perjalanan.'],
        'selesai'             => ['label'=>'Pesanan Selesai',         'icon'=>'fa-circle-check',     'desc'=>'Pesanan telah diterima.'],
        'batal'               => ['label'=>'Dibatalkan',              'icon'=>'fa-circle-xmark',     'desc'=>'Pesanan dibatalkan.'],
    ];

    public static array $steps = ['dibuat','dikemas','dikirim_ke_pengirim','dikirim','selesai'];

    public function order() { return $this->belongsTo(Order::class); }

    public static function getConfig(string $status): array {
        return static::$statusConfig[$status] ?? ['label'=>$status,'icon'=>'fa-circle','desc'=>''];
    }
}

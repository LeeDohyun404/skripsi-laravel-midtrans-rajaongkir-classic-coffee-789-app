<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderComplaint extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'jenis', 'detail', 'foto', 'status', 'catatan_operator'
    ];

    public static array $jenisConfig = [
        'barang_rusak'   => ['label' => 'Barang Rusak/Cacat',         'icon' => '💔'],
        'tidak_sesuai'   => ['label' => 'Tidak Sesuai Pesanan',        'icon' => '❌'],
        'tidak_diterima' => ['label' => 'Barang Tidak Diterima',       'icon' => '📭'],
        'terlambat'      => ['label' => 'Pengiriman Terlambat',        'icon' => '⏰'],
        'lainnya'        => ['label' => 'Lainnya',                     'icon' => '📝'],
    ];

    public static array $statusConfig = [
        'menunggu'  => ['label' => 'Menunggu Respon', 'color' => '#92400e', 'bg' => '#fef3c7'],
        'diproses'  => ['label' => 'Sedang Diproses', 'color' => '#1e40af', 'bg' => '#dbeafe'],
        'selesai'   => ['label' => 'Selesai',         'color' => '#15803d', 'bg' => '#dcfce7'],
        'ditolak'   => ['label' => 'Ditolak',         'color' => '#b91c1c', 'bg' => '#fee2e2'],
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }

    public static function getJenis(string $jenis): array {
        return static::$jenisConfig[$jenis] ?? ['label' => $jenis, 'icon' => '📝'];
    }

    public static function getStatus(string $status): array {
        return static::$statusConfig[$status] ?? ['label' => $status, 'color' => '#666', 'bg' => '#f3f4f6'];
    }
}
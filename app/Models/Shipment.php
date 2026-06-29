<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'shipments';

    protected $fillable = [
        'order_id',
        'courier_id',
        'nomor_resi',
        'alamat_pengiriman',
        'jasa_ekspedisi',
        'status_pengiriman',
    ];

    /**
     * Relasi balik ke tabel orders (Pengiriman terikat pada satu pesanan)
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Relasi ke tabel users (Mendapatkan data user dengan peran 'kurir' yang membawa barang)
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id', 'id');
    }
}

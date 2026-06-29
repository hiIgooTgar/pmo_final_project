<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'id_transaksi_gateway',
        'metode_pembayaran',
        'jumlah_dana',
        'status_pembayaran',
        'waktu_penyelesaian',
    ];

    /**
     * Relasi balik ke tabel orders (Pembayaran merujuk ke sebuah transaksi pesanan)
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}

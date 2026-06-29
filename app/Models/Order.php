<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'nomor_invoice',
        'total_pembayaran',
        'status_pesanan',
    ];

    /**
     * Relasi ke tabel users (Pesanan dimiliki oleh seorang pengguna/customer)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi ke tabel order_items (Satu pesanan memiliki banyak item produk yang dibeli)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Relasi ke tabel payments (Satu pesanan terhubung ke satu data pembayaran)
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    /**
     * Relasi ke tabel shipments (Satu pesanan terhubung ke satu data pengiriman logistik)
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'order_id', 'id');
    }
}

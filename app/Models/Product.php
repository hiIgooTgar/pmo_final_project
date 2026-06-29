<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'products';

    // Field yang diizinkan untuk pengisian massal (Mass Assignment)
    protected $fillable = [
        'kategori_produk_id',
        'nama_produk',
        'kode_produk',
        'harga',
        'stok',
        'deskripsi_produk'
    ];

    /**
     * Relasi ke tabel categories (Produk termasuk dalam sebuah kategori)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_produk_id', 'id');
    }

    /**
     * Relasi ke tabel order_items (Produk bisa dibeli di banyak item pesanan)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }
}

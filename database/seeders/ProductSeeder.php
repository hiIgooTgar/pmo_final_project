<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'kategori_produk_id' => 1,
            'nama_produk' => 'Iphone 12 Pro Max',
            'kode_produk' => 'SKU-SMART12',
            'harga' => 12500000.00,
            'stok' => 25,
            'deskripsi_produk' => 'Smartphone dengan performa tinggi untuk mobile developer.',
        ]);

        Product::create([
            'kategori_produk_id' => 2,
            'nama_produk' => 'Jaket Hoodie Polos Premium',
            'kode_produk' => 'SKU-HOODPREM',
            'harga' => 175000.00,
            'stok' => 150,
            'deskripsi_produk' => 'Jaket hoodie berbahan katun lembut dan tebal.',
        ]);

        Product::create([
            'kategori_produk_id' => 3,
            'nama_produk' => 'Beras Raja Premium 5kg',
            'kode_produk' => 'SKU-BRS5KG',
            'harga' => 82000.00,
            'stok' => 200,
            'deskripsi_produk' => 'Beras putih bersih, pulen kualitas super.',
        ]);

        Product::create([
            'kategori_produk_id' => 4,
            'nama_produk' => 'Vitamin C 1000mg Tab',
            'kode_produk' => 'SKU-VITC1000',
            'harga' => 45000.00,
            'stok' => 85,
            'deskripsi_produk' => 'Suplemen daya tahan tubuh isi 30 tablet.',
        ]);

        Product::create([
            'kategori_produk_id' => 5,
            'nama_produk' => 'Lampu Meja Belajar LED',
            'kode_produk' => 'SKU-LAMPLED',
            'harga' => 120000.00,
            'stok' => 40,
            'deskripsi_produk' => 'Lampu meja portable dengan 3 tingkat kecerahan.',
        ]);
    }
}

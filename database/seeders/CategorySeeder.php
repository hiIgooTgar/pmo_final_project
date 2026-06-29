<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'nama_kategori' => 'Elektronik',
            'kode_kategori' => 'KTG-ELK',
            'deskripsi_kategori' => 'Perangkat elektronik dan gadget modern.',
        ]);

        Category::create([
            'nama_kategori' => 'Pakaian & Fashion',
            'kode_kategori' => 'KTG-FSH',
            'deskripsi_kategori' => 'Pakaian pria, wanita, dan aksesoris fashion.',
        ]);

        Category::create([
            'nama_kategori' => 'Bahan Pokok & Makanan',
            'kode_kategori' => 'KTG-MKN',
            'deskripsi_kategori' => 'Kebutuhan pangan sehari-hari dan sembako.',
        ]);

        Category::create([
            'nama_kategori' => 'Kesehatan & Kecantikan',
            'kode_kategori' => 'KTG-KST',
            'deskripsi_kategori' => 'Produk perawatan tubuh, skincare, dan suplemen.',
        ]);

        Category::create([
            'nama_kategori' => 'Perlengkapan Rumah',
            'kode_kategori' => 'KTG-PRT',
            'deskripsi_kategori' => 'Perabotan dan dekorasi rumah tangga.',
        ]);
    }
}

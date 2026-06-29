<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Igo Tegar Prambudhy',
            'email' => 'admin.igo@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'admin',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Kec. Purwokerto Utara, Kabupaten Banyumas',
        ]);

        User::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'customer',
            'nomor_telepon' => '082134567891',
            'alamat' => 'Jl. Berkoh No. 12, Purwokerto Selatan',
        ]);

        User::create([
            'nama' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Perempuan',
            'role' => 'customer',
            'nomor_telepon' => '083134567892',
            'alamat' => 'Perumahan Arcawinangun, Purwokerto Timur',
        ]);

        User::create([
            'nama' => 'Randi Logistik',
            'email' => 'randi.kurir@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'kurir',
            'nomor_telepon' => '085134567893',
            'alamat' => 'Sokanegara, Purwokerto Timur',
        ]);

        User::create([
            'nama' => 'Agus Delivery',
            'email' => 'agus.kurir@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'kurir',
            'nomor_telepon' => '087134567894',
            'alamat' => 'Karangklesem, Purwokerto Selatan',
        ]);
    }
}

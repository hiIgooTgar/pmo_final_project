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
            'nama' => 'Admin Pasar Mobile',
            'email' => 'admin.pm@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'admin',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Jenderal Soedirman No. 1, Purwokerto Utara, Banyumas',
        ]);

        User::create([
            'nama' => 'Rahmat Hidayat',
            'email' => 'rahmat@gmail.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'customer',
            'nomor_telepon' => '082134567891',
            'alamat' => 'Jl. Berkah No. 12, Purwokerto Selatan, Banyumas',
        ]);

        User::create([
            'nama' => 'Sofyan Khoiron Mukhlis',
            'email' => 'sofyan@gmail.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'customer',
            'nomor_telepon' => '083134567892',
            'alamat' => 'Perumahan Arcawinangun, Purwokerto Timur, Banyumas',
        ]);

        User::create([
            'nama' => 'Nuril Rizqian',
            'email' => 'nuril.kurir@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'kurir',
            'nomor_telepon' => '087134567894',
            'alamat' => 'Karangklesem, Purwokerto Selatan, Banyumas',
        ]);

        User::create([
            'nama' => 'Randi Irwansyah',
            'email' => 'randi.kurir@pasarmobile.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'role' => 'kurir',
            'nomor_telepon' => '085134567893',
            'alamat' => 'Jl. Abadi No. 32, Purwokerto Timur, Banyumas',
        ]);
    }
}

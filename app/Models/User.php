<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Diperlukan untuk sistem Token API Sanctum

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel yang digunakan oleh model ini.
     * Standar Laravel menggunakan bahasa Inggris jamak (users).
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Atribut atau field tabel yang dapat diisi secara massal (Mass Assignment).
     * Disesuaikan dengan field Bahasa Indonesia yang Anda gunakan.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'jenis_kelamin',
        'role',
        'nomor_telepon',
        'alamat',
        'email_diverifikasi_pada',
    ];

    /**
     * Atribut yang harus disembunyikan saat model diubah menjadi array atau JSON.
     * Sangat penting untuk menyembunyikan password demi keamanan API.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Mengatur tipe data atau casting atribut secara otomatis.
     * Laravel 12 secara otomatis melakukan enkripsi (hash) pada password saat disimpan jika menggunakan 'hashed'.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_diverifikasi_pada' => 'datetime',
            'password' => 'hashed', // Mengamankan password secara otomatis dengan bcrypt/argon2
        ];
    }

    /**
     * Relasi ke tabel orders (Satu user dapat memiliki banyak pesanan/orders)
     * Khusus untuk user dengan peran 'customer'
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * Relasi ke tabel shipments (Satu user kurir dapat menangani banyak pengiriman)
     * Khusus untuk user dengan peran 'kurir'
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'courier_id', 'id');
    }
}

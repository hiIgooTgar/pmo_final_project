<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::create([
            'order_id' => 1,
            'id_transaksi_gateway' => 'PAY-GATEWAY-111111',
            'metode_pembayaran' => 'Bank',
            'jumlah_dana' => 12500000.00,
            'status_pembayaran' => 'sukses',
            'waktu_penyelesaian' => now(),
        ]);

        Payment::create([
            'order_id' => 2,
            'id_transaksi_gateway' => 'PAY-GATEWAY-222222',
            'metode_pembayaran' => 'QRIS',
            'jumlah_dana' => 350000.00,
            'status_pembayaran' => 'sukses',
            'waktu_penyelesaian' => now()->subHours(2),
        ]);

        Payment::create([
            'order_id' => 3,
            'id_transaksi_gateway' => 'PAY-GATEWAY-333333',
            'metode_pembayaran' => 'Bank',
            'jumlah_dana' => 82000.00,
            'status_pembayaran' => 'pending',
            'waktu_penyelesaian' => null,
        ]);

        Payment::create([
            'order_id' => 4,
            'id_transaksi_gateway' => 'PAY-GATEWAY-444444',
            'metode_pembayaran' => 'QRIS',
            'jumlah_dana' => 135000.00,
            'status_pembayaran' => 'sukses',
            'waktu_penyelesaian' => now()->subDay(),
        ]);

        Payment::create([
            'order_id' => 5,
            'id_transaksi_gateway' => 'PAY-GATEWAY-555555',
            'metode_pembayaran' => 'QRIS',
            'jumlah_dana' => 240000.00,
            'status_pembayaran' => 'sukses',
            'waktu_penyelesaian' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $order1 = Order::create([
            'user_id' => 2,
            'nomor_invoice' => 'INV-20260629-AAA01',
            'total_pembayaran' => 12500000.00,
            'status_pesanan' => 'dibayar',
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 1,
            'qty' => 1,
            'harga_beli' => 12500000.00,
        ]);

        $order2 = Order::create([
            'user_id' => 2,
            'nomor_invoice' => 'INV-20260629-BBB02',
            'total_pembayaran' => 350000.00,
            'status_pesanan' => 'dikirim',
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => 2,
            'qty' => 2,
            'harga_beli' => 175000.00,
        ]);

        $order3 = Order::create([
            'user_id' => 3,
            'nomor_invoice' => 'INV-20260629-CCC03',
            'total_pembayaran' => 82000.00,
            'status_pesanan' => 'menunggu_pembayaran',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => 3,
            'qty' => 1,
            'harga_beli' => 82000.00,
        ]);

        $order4 = Order::create([
            'user_id' => 3,
            'nomor_invoice' => 'INV-20260629-DDD04',
            'total_pembayaran' => 135000.00,
            'status_pesanan' => 'selesai',
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => 4,
            'qty' => 3,
            'harga_beli' => 45000.00,
        ]);

        $order5 = Order::create([
            'user_id' => 2,
            'nomor_invoice' => 'INV-20260629-EEE05',
            'total_pembayaran' => 240000.00,
            'status_pesanan' => 'diproses',
        ]);
        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => 5,
            'qty' => 2,
            'harga_beli' => 120000.00,
        ]);
    }
}

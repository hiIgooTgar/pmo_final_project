<?php

namespace Database\Seeders;

use App\Models\Shipment;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        Shipment::create([
            'order_id' => 1,
            'courier_id' => null,
            'nomor_resi' => 'RESI-JNE-MANUAL001',
            'alamat_pengiriman' => 'Jl. Berkah No. 12, Purwokerto Selatan, Banyumas',
            'jasa_ekspedisi' => 'JNE',
            'status_pengiriman' => 'manifest',
        ]);

        Shipment::create([
            'order_id' => 2,
            'courier_id' => 4,
            'nomor_resi' => 'RESI-JNT-MANUAL002',
            'alamat_pengiriman' => 'Jl. Berkah No. 12, Purwokerto Selatan, Banyumas',
            'jasa_ekspedisi' => 'J&T Express',
            'status_pengiriman' => 'sedang_dikirim',
        ]);

        Shipment::create([
            'order_id' => 3,
            'courier_id' => null,
            'nomor_resi' => null,
            'alamat_pengiriman' => 'Perumahan Arcawinangun, Purwokerto Timur, Banyumas',
            'jasa_ekspedisi' => 'SiCepat',
            'status_pengiriman' => 'manifest',
        ]);

        Shipment::create([
            'order_id' => 4,
            'courier_id' => 5,
            'nomor_resi' => 'RESI-SICEPAT-MANUAL004',
            'alamat_pengiriman' => 'Perumahan Arcawinangun, Purwokerto Timur, Banyumas',
            'jasa_ekspedisi' => 'SiCepat',
            'status_pengiriman' => 'sampai_tujuan',
        ]);

        Shipment::create([
            'order_id' => 5,
            'courier_id' => 4,
            'nomor_resi' => 'RESI-JNE-MANUAL005',
            'alamat_pengiriman' => 'Jl. Berkah No. 12, Purwokerto Selatan, Banyumas',
            'jasa_ekspedisi' => 'JNE',
            'status_pengiriman' => 'dalam_proses',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    /**
     * 1. Menampilkan seluruh data pengiriman barang (Hanya Admin / Kurir)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $shipments = Shipment::with(['order.user', 'courier'])->latest()->get();
        } else {
            // Jika yang login adalah kurir, tampilkan tugas pengiriman miliknya saja
            $shipments = Shipment::with('order.user')->where('courier_id', $user->id)->latest()->get();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data riwayat pengiriman logistik berhasil diambil.',
            'data' => $shipments
        ], 200);
    }

    /**
     * =======================================================================
     * MANDATORY INTEGRASI KASUS 3 (LOGISTIK): Endpoint Post Shipment (POST)
     * URL: /api/shipment
     * =======================================================================
     */
    public function postShipment(Request $request)
    {
        $messages = [
            'order_id.required' => 'ID Pesanan wajib dilampirkan untuk proses pengiriman.',
            'order_id.exists' => 'ID Pesanan tidak valid atau tidak ditemukan.',
            'order_id.unique' => 'ID Pesanan tersebut sudah terdaftar dalam proses pengiriman kurir.',
            'alamat_pengiriman.required' => 'Alamat tujuan pengiriman wajib diisi.',
            'alamat_pengiriman.string' => 'Alamat pengiriman harus berupa teks.',
            'jasa_ekspedisi.required' => 'Nama jasa ekspedisi kurir wajib diisi.',
            'jasa_ekspedisi.string' => 'Nama jasa ekspedisi harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id|unique:shipments,order_id',
            'alamat_pengiriman' => 'required|string|max:500',
            'jasa_ekspedisi' => 'required|string|max:100',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses pengiriman gagal, data yang dikirimkan tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::find($request->order_id);

            // Validasi tambahan: Pastikan order sudah dibayar sebelum dikirim ke logistik kurir
            if ($order->status_pesanan === 'menunggu_pembayaran') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Gagal memproses pengiriman. Transaksi ini belum diselesaikan atau belum dibayar.'
                ], 400);
            }

            // Generate Nomor Resi Otomatis Simulasi Sistem Kurir (Contoh: RESI-JNE-XXXXXXXX)
            $cleanEkspedisi = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', $request->jasa_ekspedisi));
            $nomorResi = 'RESI-' . $cleanEkspedisi . '-' . strtoupper(Str::random(10));

            // Buat data manifest pengiriman baru
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'courier_id' => null, // Belum ditugaskan ke kurir spesifik (masih berstatus manifest)
                'nomor_resi' => $nomorResi,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'jasa_ekspedisi' => $request->jasa_ekspedisi,
                'status_pengiriman' => 'manifest',
            ]);

            // Ubah status pesanan di tabel orders menjadi 'dikirim' atau 'diproses' logistik
            $order->update(['status_pesanan' => 'dikirim']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengiriman berhasil dikirimkan ke sistem logistik kurir.',
                'data' => $shipment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'Terjadi kegagalan internal sistem saat memproses data pengiriman.',
                'error_debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. Update Status Logistik & Resi (Oleh Role Kurir saat mengantar barang)
     */
    public function updateStatusLogistik(Request $request, $id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data logistik pengiriman tidak ditemukan.'
            ], 404);
        }

        $messages = [
            'status_pengiriman.required' => 'Status pengiriman terbaru wajib diisi.',
            'status_pengiriman.in' => 'Pilihan status pengiriman tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'status_pengiriman' => 'required|in:manifest,dalam_proses,sedang_dikirim,sampai_tujuan,gagal_kirim',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Gagal memperbarui status pengiriman logistik.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = $request->user();

            // Otomatis plot courier_id jika kurir yang mengambil tugas mengupdate data pertama kali
            $updateData = ['status_pengiriman' => $request->status_pengiriman];
            if ($user->role === 'kurir' && is_null($shipment->courier_id)) {
                $updateData['courier_id'] = $user->id;
            }

            $shipment->update($updateData);

            // Integrasi otomatis balik ke status pesanan di tabel orders jika barang sudah sampai tujuan
            if ($request->status_pengiriman === 'sampai_tujuan') {
                Order::where('id', $shipment->order_id)->update(['status_pesanan' => 'selesai']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Status pelacakan kurir logistik berhasil diperbarui.',
                'data' => $shipment
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'Kesalahan internal gagal memperbarui tracking kurir.',
                'error_debug' => $e->getMessage()
            ], 500);
        }
    }
}

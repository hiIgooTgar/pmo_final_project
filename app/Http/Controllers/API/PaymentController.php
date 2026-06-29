<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * 1. Menampilkan semua riwayat transaksi pembayaran (Hanya Admin)
     */
    public function index()
    {
        $payments = Payment::with('order.user')->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data seluruh riwayat transaksi pembayaran berhasil diambil.',
            'data' => $payments
        ], 200);
    }

    /**
     * 2. Simulasi Request Pembayaran Awal (Oleh Customer saat memilih metode bayar)
     */
    public function requestPayment(Request $request)
    {
        $messages = [
            'order_id.required' => 'ID Pesanan wajib dilampirkan.',
            'order_id.exists' => 'ID Pesanan tidak valid atau tidak ditemukan.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus berupa Bank atau QRIS.',
        ];

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'metode_pembayaran' => 'required|in:Bank,QRIS',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi permintaan pembayaran gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::find($request->order_id);

        // Keamanan: Pastikan pesanan tersebut adalah milik customer yang sedang login
        if ($request->user()->role === 'customer' && $order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Anda tidak memiliki hak akses untuk memproses pembayaran pesanan ini.'
            ], 403);
        }

        // Cek jika sudah pernah dibuatkan data payment sebelumnya
        $existingPayment = Payment::where('order_id', $order->id)->first();
        if ($existingPayment && $existingPayment->status_pembayaran === 'sukses') {
            return response()->json([
                'status' => 'fail',
                'message' => 'Pesanan ini sudah berstatus lunas dan berhasil dibayar.'
            ], 400);
        }

        // Generate ID Transaksi Gateway Simulasi (Contoh: PAY-GATEWAY-682394)
        $idGateway = 'PAY-GATEWAY-' . rand(100000, 999999);

        // Simpan data pembayaran awal dengan status pending
        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'id_transaksi_gateway' => $idGateway,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_dana' => $order->total_pembayaran,
                'status_pembayaran' => 'pending',
                'waktu_penyelesaian' => null
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan pembayaran berhasil dibuat. Silakan simulasikan pembayaran via webhook.',
            'data' => $payment
        ], 201);
    }

    /**
     * =======================================================================
     * KASUS INTEGRASI 2 (PAYMENT GATEWAY): Endpoint Webhook/Callback (POST)
     * URL: /api/payment/callback
     * =======================================================================
     */
    public function handleCallback(Request $request)
    {
        $messages = [
            'id_transaksi_gateway.required' => 'ID transaksi gateway wajib dikirimkan oleh sistem payment gateway.',
            'id_transaksi_gateway.exists' => 'ID transaksi gateway tidak dikenali di sistem kami.',
            'status_pembayaran.required' => 'Status pembayaran terbaru wajib dilampirkan.',
            'status_pembayaran.in' => 'Status pembayaran harus bernilai pending, sukses, atau gagal.',
        ];

        $validator = Validator::make($request->all(), [
            'id_transaksi_gateway' => 'required|exists:payments,id_transaksi_gateway',
            'status_pembayaran' => 'required|in:pending,sukses,gagal',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Notifikasi webhook payment gateway tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Menjalankan Database Transaction demi menjaga konsistensi dua tabel terintegrasi
        DB::beginTransaction();

        try {
            $payment = Payment::where('id_transaksi_gateway', $request->id_transaksi_gateway)->first();
            $order = Order::find($payment->order_id);

            // Perbarui data tabel payments
            $payment->status_pembayaran = $request->status_pembayaran;
            if ($request->status_pembayaran === 'sukses') {
                $payment->waktu_penyelesaian = now();

                // OTOMATISASI INTEGRASI: Mengubah status di tabel orders menjadi 'dibayar'
                $order->status_pesanan = 'dibayar';
            } elseif ($request->status_pembayaran === 'gagal') {
                $order->status_pesanan = 'dibatalkan';
            }

            $payment->save();
            $order->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook Payment Gateway berhasil diproses. Sinkronisasi status pesanan sukses dilakukan.',
                'data' => [
                    'id_transaksi_gateway' => $payment->id_transaksi_gateway,
                    'status_pembayaran' => $payment->status_pembayaran,
                    'status_pesanan_terbaru' => $order->status_pesanan
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'Gagal memproses webhook akibat kesalahan sistem internal.',
                'error_debug' => $e->getMessage()
            ], 500);
        }
    }
}

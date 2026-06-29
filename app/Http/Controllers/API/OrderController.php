<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * 1. Menampilkan riwayat pesanan (Customer melihat miliknya, Admin melihat semua)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika admin, tampilkan semua pesanan dalam sistem. Jika customer, tampilkan miliknya saja.
        if ($user->role === 'admin') {
            $orders = Order::with(['user', 'orderItems.product'])->latest()->get();
        } else {
            $orders = Order::with('orderItems.product')->where('user_id', $user->id)->latest()->get();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data riwayat pesanan berhasil diambil.',
            'data' => $orders
        ], 200);
    }

    /**
     * 2. Membuat Pesanan Baru (Checkout Checkout oleh Customer)
     */
    public function store(Request $request)
    {
        $messages = [
            'items.required' => 'Daftar item produk yang dibeli wajib dilampirkan.',
            'items.array' => 'Format daftar item harus berupa susunan array produk.',
            'items.*.product_id.required' => 'ID Produk pada item wajib diisi.',
            'items.*.product_id.exists' => 'ID Produk tidak valid atau tidak ditemukan.',
            'items.*.qty.required' => 'Jumlah kuantitas (qty) produk wajib diisi.',
            'items.*.qty.integer' => 'Jumlah kuantitas harus berupa bilangan bulat.',
            'items.*.qty.min' => 'Jumlah kuantitas minimal pembelian adalah 1.',
        ];

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi pembuatan pesanan gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Menggunakan Database Transaction untuk mencegah inkonsistensi data jika server error di tengah jalan
        DB::beginTransaction();

        try {
            $user = $request->user();
            $totalPembayaran = 0;
            $orderItemsData = [];

            // 1. Validasi ketersediaan stok barang dan kalkulasi total harga secara backend (aman dari manipulasi)
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stok < $item['qty']) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => "Stok produk '" . $product->nama_produk . "' tidak mencukupi. Stok tersedia saat ini: " . $product->stok
                    ], 400);
                }

                $hargaBeli = $product->harga;
                $subTotal = $hargaBeli * $item['qty'];
                $totalPembayaran += $subTotal;

                // Tampung data item untuk disisipkan nanti
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'harga_beli' => $hargaBeli,
                    'product_model' => $product // simpan instansiasi objek untuk potong stok
                ];
            }

            // 2. Generate Nomor Invoice Unik (Contoh: INV-20260629-XXXXX)
            $nomorInvoice = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // 3. Buat Data Utama Transaksi Pesanan (Order)
            $order = Order::create([
                'user_id' => $user->id,
                'nomor_invoice' => $nomorInvoice,
                'total_pembayaran' => $totalPembayaran,
                'status_pesanan' => 'menunggu_pembayaran',
            ]);

            // 4. Masukkan Item Detail & Potong Stok Produk di Gudang
            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'qty' => $itemData['qty'],
                    'harga_beli' => $itemData['harga_beli'],
                ]);

                // Update pengurangan stok produk
                $productModel = $itemData['product_model'];
                $productModel->decrement('stok', $itemData['qty']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat, silakan lakukan proses pembayaran.',
                'data' => $order->load('orderItems.product')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'Terjadi kesalahan sistem internal saat memproses pesanan.',
                'error_debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. Menampilkan detail satu pesanan berdasarkan ID
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $order = Order::with(['user', 'orderItems.product', 'payment', 'shipment'])->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data pesanan tidak ditemukan.'
            ], 404);
        }

        // Keamanan: Pastikan customer tidak bisa mengintip pesanan customer lain
        if ($user->role === 'customer' && $order->user_id !== $user->id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Anda tidak memiliki hak akses untuk melihat data pesanan ini.'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pesanan berhasil ditemukan.',
            'data' => $order
        ], 200);
    }

    /**
     * 4. Mengubah status pesanan secara manual (Hanya Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data pesanan tidak ditemukan.'
            ], 404);
        }

        $messages = [
            'status_pesanan.required' => 'Status pesanan baru wajib diisi.',
            'status_pesanan.in' => 'Pilihan status pesanan tidak valid.',
        ];

        $validator = Validator::make($request->all(), [
            'status_pesanan' => 'required|in:menunggu_pembayaran,dibayar,diproses,dikirim,selesai,dibatalkan',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validasi pembaruan status pesanan gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update([
            'status_pesanan' => $request->status_pesanan
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan berhasil diperbarui.',
            'data' => $order
        ], 200);
    }
}

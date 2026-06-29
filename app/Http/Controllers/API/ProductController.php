<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * 1. Menampilkan semua data produk (Katalog PasarMobile)
     */
    public function index()
    {
        // Mengambil produk beserta data kategori terkait
        $products = Product::with('category')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data seluruh produk berhasil diambil.',
            'data' => $products
        ], 200);
    }

    /**
     * 2. Menyimpan data produk baru (Hanya Admin)
     */
    public function store(Request $request)
    {
        $messages = [
            'kategori_produk_id.required' => 'Kategori produk wajib dipilih.',
            'kategori_produk_id.exists' => 'Kategori produk yang dipilih tidak valid atau tidak terdaftar.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.string' => 'Nama produk harus berupa teks.',
            'kode_produk.required' => 'Kode produk wajib diisi.',
            'kode_produk.unique' => 'Kode produk tersebut sudah terdaftar dalam sistem gudang.',
            'harga.required' => 'Harga produk wajib diisi.',
            'harga.numeric' => 'Harga produk harus berupa angka.',
            'harga.min' => 'Harga produk tidak boleh kurang dari 0.',
            'stok.integer' => 'Jumlah stok harus berupa bilangan bulat.',
            'stok.min' => 'Jumlah stok tidak boleh kurang dari 0.',
            'deskripsi_produk.string' => 'Deskripsi produk harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:categories,id',
            'nama_produk' => 'required|string|max:255',
            'kode_produk' => 'required|string|unique:products,kode_produk',
            'harga' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'deskripsi_produk' => 'nullable|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi pengisian data produk gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create([
            'kategori_produk_id' => $request->kategori_produk_id,
            'nama_produk' => $request->nama_produk,
            'kode_produk' => $request->kode_produk,
            'harga' => $request->harga,
            'stok' => $request->stok ?? 0,
            'deskripsi_produk' => $request->deskripsi_produk,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk baru berhasil ditambahkan ke dalam sistem.',
            'data' => $product
        ], 201);
    }

    /**
     * 3. Menampilkan detail satu produk berdasarkan ID
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data produk tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data produk berhasil ditemukan.',
            'data' => $product
        ], 200);
    }

    /**
     * 4. Memperbarui data produk (Hanya Admin)
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data produk yang ingin diperbarui tidak ditemukan.'
            ], 404);
        }

        $messages = [
            'kategori_produk_id.required' => 'Kategori produk wajib dipilih.',
            'kategori_produk_id.exists' => 'Kategori produk tidak valid.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'kode_produk.required' => 'Kode produk wajib diisi.',
            'kode_produk.unique' => 'Kode produk tersebut sudah digunakan oleh produk lain.',
            'harga.required' => 'Harga produk wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'stok.integer' => 'Stok harus berupa bilangan bulat.',
        ];

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:categories,id',
            'nama_produk' => 'required|string|max:255',
            'kode_produk' => 'required|string|unique:products,kode_produk,' . $id,
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi_produk' => 'nullable|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi pembaruan data produk gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data produk berhasil diperbarui.',
            'data' => $product
        ], 200);
    }

    /**
     * 5. Menghapus produk (Hanya Admin)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data produk yang ingin dihapus tidak ditemukan.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data produk berhasil dihapus dari sistem.'
        ], 200);
    }

    /**
     * =======================================================================
     * SKENARIO INTEGRASI KASUS 1 (GUDANG): Endpoint Check Stock (GET)
     * URL: /api/stock/{id}
     * =======================================================================
     */
    public function checkStock($id)
    {
        $product = Product::find($id);

        // Jika data barang/produk tidak ada di gudang
        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Pengecekan gagal, produk dengan ID tersebut tidak ditemukan di sistem gudang.'
            ], 404);
        }

        // Response Berhasil Sesuai Kebutuhan Integrasi Real-Time PasarMobile
        return response()->json([
            'status' => 'success',
            'message' => 'Sinkronisasi ketersediaan stok barang berhasil didapatkan.',
            'data' => [
                'product_id' => $product->kode_produk, // Mengembalikan kode produk unik (SKU)
                'stock_quantity' => $product->stok      // Jumlah stok di database backend saat ini
            ]
        ], 200);
    }
}

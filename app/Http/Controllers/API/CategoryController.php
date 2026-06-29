<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data semua kategori berhasil diambil.',
            'data' => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $messages = [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.unique' => 'Nama kategori tersebut sudah terdaftar.',
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.string' => 'Kode kategori harus berupa teks.',
            'deskripsi_kategori.string' => 'Deskripsi kategori harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|unique:categories,nama_kategori',
            'kode_kategori' => 'required|string',
            'deskripsi_kategori' => 'nullable|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi penambahan kategori gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'nama_kategori' => $request->nama_kategori,
            'kode_kategori' => $request->kode_kategori,
            'deskripsi_kategori' => $request->deskripsi_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori baru berhasil ditambahkan.',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data kategori tidak ditemukan dalam sistem.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data kategori berhasil ditemukan.',
            'data' => $category
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data kategori yang ingin diperbarui tidak ditemukan.'
            ], 404);
        }

        $messages = [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.unique' => 'Nama kategori tersebut sudah digunakan oleh kategori lain.',
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.string' => 'Kode kategori harus berupa teks.',
            'deskripsi_kategori.string' => 'Deskripsi kategori harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|unique:categories,nama_kategori,' . $id,
            'kode_kategori' => 'required|string',
            'deskripsi_kategori' => 'nullable|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi pembaruan kategori gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'kode_kategori' => $request->kode_kategori,
            'deskripsi_kategori' => $request->deskripsi_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data kategori berhasil diperbarui.',
            'data' => $category
        ], 200);
    }


    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data kategori yang ingin dihapus tidak ditemukan.'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Data kategori berhasil dihapus dari sistem.'
        ], 200);
    }
}

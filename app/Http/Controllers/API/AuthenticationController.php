<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $messages = [
            'nama.required' => 'Kolom nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Alamat email tersebut sudah terdaftar dalam sistem.',
            'password.required' => 'Kolom kata sandi wajib diisi.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 8 karakter.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin harus berupa Laki-laki atau Perempuan.',
            'nomor_telepon.string' => 'Nomor telepon harus berupa teks.',
            'alamat.string' => 'Alamat harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nomor_telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Proses validasi data gagal dilakukan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'role' => 'customer',
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'message' => 'Proses registrasi pengguna berhasil dilakukan.',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $messages = [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Kolom email dan kata sandi wajib diisi dengan benar.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Kredensial yang Anda berikan salah atau tidak cocok dengan data kami.'
            ], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Proses autentikasi login berhasil dilakukan.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Proses logout berhasil dilakukan, token autentikasi telah dihapus.'
        ], 200);
    }
}

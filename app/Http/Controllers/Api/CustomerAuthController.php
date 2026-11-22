<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,11}$/',
                Rule::unique('customers', 'no_hp'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP harus 10–13 digit dan diawali 08.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $customer = Customer::create([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil.',
            'customer' => [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'no_hp' => $customer->no_hp,
                'must_change_password' => $customer->must_change_password,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'no_hp' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,11}$/',
            ],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('no_hp', $validated['no_hp'])->first();

        if (!$customer || !Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'message' => 'Nomor HP atau password salah.',
            ], 401);
        }

        $token = $customer->createToken('customer-app')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'customer' => [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'no_hp' => $customer->no_hp,
                'must_change_password' => $customer->must_change_password,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function profile(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'message' => 'Profil pelanggan berhasil diambil.',
            'customer' => [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'no_hp' => $customer->no_hp,
                'must_change_password' => $customer->must_change_password,
            ],
        ]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $customer = $request->user();

        if (!Hash::check($validated['password_lama'], $customer->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $customer->update([
            'password' => Hash::make($validated['password_baru']),
            'must_change_password' => false,
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
            'must_change_password' => $customer->fresh()->must_change_password,
        ]);
    }
}

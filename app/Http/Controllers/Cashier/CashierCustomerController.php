<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CashierCustomerController extends Controller
{
    public function index()
    {
        return view('cashier.customers.reset-password');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'no_hp' => ['required', 'digits_between:10,15'],
        ], [
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.digits_between' => 'Nomor HP harus terdiri dari 10 sampai 15 angka.',
        ]);

        $customer = Customer::where('no_hp', $request->no_hp)->first();

        if (!$customer) {
            return back()
                ->withInput()
                ->with('error', 'Pelanggan dengan nomor HP tersebut tidak ditemukan.');
        }

        $passwordSementara = Str::random(10);

        $customer->update([
            'password' => Hash::make($passwordSementara),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('cashier.customers.reset-password')
            ->with('success', 'Password pelanggan berhasil direset.')
            ->with('password_sementara', $passwordSementara)
            ->with('nama_customer', $customer->nama);
    }
}

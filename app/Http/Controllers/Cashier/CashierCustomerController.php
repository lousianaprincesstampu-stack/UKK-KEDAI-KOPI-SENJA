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
            'no_hp' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,11}$/',
            ],
        ], [
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP harus 10–13 digit dan diawali 08.',
        ]);

        $customer = Customer::where('no_hp', $request->no_hp)->first();

        if (!$customer) {
            return back()
                ->withInput()
                ->with('error', 'Pelanggan dengan nomor HP tersebut tidak ditemukan.');
        }

        $passwordSementara = Str::random(10);

        $customer->password = Hash::make($passwordSementara);
        $customer->must_change_password = true;
        $customer->save();

        $customer->refresh();

        if (!Hash::check($passwordSementara, $customer->password)) {
            return back()->with('error', 'Password gagal disimpan.');
        }

        return redirect()
            ->route('cashier.customers.reset-password')
            ->with('success', 'Password pelanggan berhasil direset.')
            ->with('password_sementara', $passwordSementara)
            ->with('nama_customer', $customer->nama);
    }
}

<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CashierOrderController extends Controller
{
    public function index()
    {
        $pesanan = Order::with(['customer', 'cafeTable'])
            ->whereIn('status_pesanan', ['Baru', 'Diproses'])
            ->orderByDesc('tanggal_pesanan')
            ->get();

        return view('cashier.orders.index', compact('pesanan'));
    }


    public function list(Request $request)
    {
        $query = Order::with(['customer', 'cafeTable']);

        if ($request->filled('cari')) {
            $query->where('kode_pesanan', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_pesanan', $request->status);
        }

        $pesanan = $query->orderByDesc('tanggal_pesanan')->get();

        return view('cashier.orders.list', compact('pesanan'));
    }


    public function show($id)
    {
        $pesanan = Order::with([
            'customer',
            'cafeTable',
            'orderDetails'
        ])->findOrFail($id);

        return view('cashier.orders.show', compact('pesanan'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pesanan' => 'required|in:Diproses,Selesai',
        ]);

        $pesanan = Order::findOrFail($id);

        $pesanan->update([
            'status_pesanan' => $request->status_pesanan,
        ]);

        return redirect()
            ->route('cashier.orders.show', $pesanan->id)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

}

<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class CashierController extends Controller
{
    public function index()
    {
        // Menghitung pesanan berdasarkan status.
        $pesananBaru = Order::where('status_pesanan', 'Baru')->count();

        $pesananDiproses = Order::where('status_pesanan', 'Diproses')->count();

        $pesananSelesai = Order::where('status_pesanan', 'Selesai')
            ->whereDate('tanggal_pesanan', today())
            ->count();

        $pesananTerbaru = Order::with(['customer', 'cafeTable'])
            ->orderByDesc('tanggal_pesanan')
            ->take(5)
            ->get();

        return view('cashier.dashboard', compact(
            'pesananBaru',
            'pesananDiproses',
            'pesananSelesai',
            'pesananTerbaru'
        ));
    }
}

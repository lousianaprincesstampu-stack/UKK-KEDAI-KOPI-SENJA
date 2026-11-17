<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Order;

class ManagerController extends Controller
{
    public function index()
    {
        $totalMenu = Menu::count();
        $totalBahan = Bahan::count();

        $penjualanHariIni = Order::where('status_pesanan', 'Selesai')
            ->where('status_pembayaran', 'Dibayar')
            ->whereDate('tanggal_pesanan', today())
            ->sum('total');

        $bahanMenipis = Bahan::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('nama_bahan', 'asc')
            ->get();

        return view('manager.dashboard', compact(
            'totalMenu',
            'totalBahan',
            'penjualanHariIni',
            'bahanMenipis'
        ));
    }
}

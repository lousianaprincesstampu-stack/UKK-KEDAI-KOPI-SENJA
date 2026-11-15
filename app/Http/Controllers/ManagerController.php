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

        // Menghitung penjualan hari ini. Hanya pesanan yang sudah selesai dan sudah dibayar.
        $penjualanHariIni = Order::where('status_pesanan', 'Selesai')
            ->where('status_pembayaran', 'Dibayar')
            ->whereDate('tanggal_pesanan', today())
            ->sum('total');

        // Mengambil bahan yang stoknya sama dengan atau di bawah stok minimum.
        $bahanMenipis = Bahan::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('nama_bahan', 'asc')
            ->get();

        // Mengirim semua data ke halaman Dashboard.
        return view('manager.dashboard', compact(
            'totalMenu',
            'totalBahan',
            'penjualanHariIni',
            'bahanMenipis'
        ));
    }
}

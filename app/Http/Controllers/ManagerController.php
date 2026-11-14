<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Order;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        $totalMenu::count();
        $totalBahan::count();
        
         // Menghitung total penjualan hari ini. Hanya pesanan yang sudah selesai dan sudah dibayar.
         $penjualanHariIni = order::where('status_pesanan', 'selesai')
         ->where('status_pembayaran', 'dibayar')
         ->whereDAte('tanggal_pesanan', today())
         ->sum('total'); //menjumlahkan nilai transaksi yang memenuhi ketiga kondisi tersebut.

         return view('manager.dashboard', compact( //mengirim hasil perhitungan ke Blade.
            'totalMenu',
            'totalBahan',
            'PenjualanHariIni',
         ));
    }
}

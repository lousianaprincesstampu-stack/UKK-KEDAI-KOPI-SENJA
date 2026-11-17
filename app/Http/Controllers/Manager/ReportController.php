<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Bahan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function sales(Request $request)
    {
        $tanggalAwal = $request->input(
            'tanggal_awal',
            Carbon::now()->startOfMonth()->toDateString()
        );

        $tanggalAkhir = $request->input(
            'tanggal_akhir',
            Carbon::now()->toDateString()
        );

        $penjualan = OrderDetail::query()
            ->whereHas('order', function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->where('status_pesanan', 'Selesai')
                    ->where('status_pembayaran', 'Dibayar')
                    ->whereBetween('tanggal_pesanan', [
                        Carbon::parse($tanggalAwal)->startOfDay(),
                        Carbon::parse($tanggalAkhir)->endOfDay(),
                    ]);
            })

            ->selectRaw('
                nama_menu,
                SUM(jumlah) as total_terjual,
                SUM(subtotal) as total_penjualan
            ')

            ->groupBy('nama_menu')
            // Mengurutkan dari menu paling banyak terjual.
            ->orderByDesc('total_terjual')
            ->get();

        $totalPenjualan = $penjualan->sum('total_penjualan');

        $totalMenuTerjual = $penjualan->sum('total_terjual');

        return view('manager.reports.sales', compact(
            'penjualan',
            'tanggalAwal',
            'tanggalAkhir',
            'totalPenjualan',
            'totalMenuTerjual'
        ));

    }

    //laporan stok bahan yang menipis.
    public function lowStock()
    {
        $bahanMenipis = Bahan::whereColumn('stok', '<=', 'stok_minimum')

            ->orderBy('stok', 'asc')
            ->get();

        $totalBahanMenipis = $bahanMenipis->count();

        return view('manager.reports.low-stock', compact(
            'bahanMenipis',
            'totalBahanMenipis'
        ));
    }

    public function margin()
    {
        $menus = \App\Models\Menu::with('recipe.recipeDetails.bahan')
            ->get();

        $laporanMargin = $menus->map(function ($menu) {

            //Mmenghitung total HPP berdasarkan jumlah bahan dikali harga per satuan.
            $hpp = $menu->recipe
                ? $menu->recipe->recipeDetails->sum(function ($detail) {
                    return $detail->jumlah * ($detail->bahan->harga_per_satuan ?? 0);
                })
                : 0;

            // Menghitung margin dari harga jual dikurangi HPP.
            $margin = $menu->harga_jual - $hpp;

            return [
                'nama_menu' => $menu->nama_menu,
                'harga_jual' => $menu->harga_jual,
                'hpp' => $hpp,
                'margin' => $margin,
            ];
        });

        return view('manager.reports.margin', compact('laporanMargin'));
    }


}

<?php

namespace App\Services;

use App\Models\Menu;

class MenuStockService
{
    public function perbaruiSemuaStatus(): void
    {
        $menus = Menu::with('recipe.recipeDetails.bahan')->get();

        foreach ($menus as $menu) {

        if (in_array($menu->nama_menu, ['Latte', 'Mocha'])) {
                $menu->update([
                    'status_stok' => 'Tidak Tersedia',
                ]);

                continue;
            }

            // Menu tanpa resep atau resep kosong tidak dapat dipesan.
            if (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                $menu->update([
                    'status_stok' => 'Tidak Tersedia',
                ]);

                continue;
            }

            $kebutuhanBahan = [];
            $resepTidakLengkap = false;

            foreach ($menu->recipe->recipeDetails as $detail) {

                // Memeriksa apakah bahan pada resep masih tersedia di database.
                if (!$detail->bahan) {
                    $resepTidakLengkap = true;
                    break;
                }

                $bahanId = $detail->bahan->id;

                // Menggabungkan kebutuhan jika bahan yang sama muncul beberapa kali.
                $kebutuhanBahan[$bahanId] =
                    ($kebutuhanBahan[$bahanId] ?? 0) + (float) $detail->jumlah;
            }

            // Resep tidak lengkap tidak boleh dianggap sebagai stok habis.
            if ($resepTidakLengkap) {
                $menu->update([
                    'status_stok' => 'Tidak Tersedia',
                ]);

                continue;
            }

            $bahanTidakCukup = false;

            foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                $bahan = $menu->recipe->recipeDetails
                    ->firstWhere('bahan_id', $bahanId)?->bahan;

                // Memeriksa stok setiap bahan berdasarkan kebutuhan resep.
                if (!$bahan || (float) $bahan->stok < $jumlahDibutuhkan) {
                    $bahanTidakCukup = true;
                    break;
                }
            }

            // Menentukan status akhir berdasarkan ketersediaan stok bahan.
            $menu->update([
                'status_stok' => $bahanTidakCukup ? 'Habis' : 'Tersedia',
            ]);
        }
    }
}

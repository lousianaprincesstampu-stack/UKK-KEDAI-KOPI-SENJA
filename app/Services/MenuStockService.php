<?php

namespace App\Services;

use App\Models\Menu;

class MenuStockService
{
    public function perbaruiSemuaStatus(): void
    {
        $menus = Menu::with('recipe.recipeDetails.bahan')->get();

        foreach ($menus as $menu) {

            // Pertahankan status menu yang resepnya belum lengkap.
            if ($menu->status_stok === 'Tidak Tersedia') {
                continue;
            }

            if (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                $menu->update([
                    'status_stok' => 'Habis',
                ]);

                continue;
            }

            $kebutuhanBahan = [];
            $bahanTidakDitemukan = false;

            foreach ($menu->recipe->recipeDetails as $detail) {

                if (!$detail->bahan) {
                    $bahanTidakDitemukan = true;
                    break;
                }

                $bahanId = $detail->bahan->id;

                $kebutuhanBahan[$bahanId] =
                    ($kebutuhanBahan[$bahanId] ?? 0) + (float) $detail->jumlah;
            }

            $bahanTidakCukup = $bahanTidakDitemukan;

            if (!$bahanTidakCukup) {
                foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                    $bahan = $menu->recipe->recipeDetails
                        ->firstWhere('bahan_id', $bahanId)?->bahan;

                    if (!$bahan || (float) $bahan->stok < $jumlahDibutuhkan) {
                        $bahanTidakCukup = true;
                        break;
                    }
                }
            }

            $menu->update([
                'status_stok' => $bahanTidakCukup ? 'Habis' : 'Tersedia',
            ]);
        }
    }
}

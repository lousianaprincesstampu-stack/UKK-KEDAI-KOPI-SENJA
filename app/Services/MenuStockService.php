<?php

namespace App\Services;

use App\Models\Menu;

class MenuStockService
{
    public function perbaruiSemuaStatus(): void
    {
        $menus = Menu::with('recipe.recipeDetails.bahan')->get();

        foreach ($menus as $menu) {

            if (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                continue;
            }

            $kebutuhanBahan = [];

            foreach ($menu->recipe->recipeDetails as $detail) {

                if (!$detail->bahan) {
                    $kebutuhanBahan[-1] = 1;
                    continue;
                }

                $bahanId = $detail->bahan->id;

                $kebutuhanBahan[$bahanId] =
                    ($kebutuhanBahan[$bahanId] ?? 0) + (float) $detail->jumlah;
            }

            $bahanTidakCukup = false;

            foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                $bahan = $menu->recipe->recipeDetails
                    ->firstWhere('bahan_id', $bahanId)?->bahan;

                if (!$bahan || (float) $bahan->stok < $jumlahDibutuhkan) {
                    $bahanTidakCukup = true;
                    break;
                }
            }

            $menu->update([
                'status_stok' => $bahanTidakCukup ? 'Habis' : 'Tersedia',
            ]);
        }
    }
}

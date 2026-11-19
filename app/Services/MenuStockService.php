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

            $bahanTidakCukup = $menu->recipe->recipeDetails->contains(
                function ($detail) {
                    if (!$detail->bahan) {
                        return true;
                    }

                    return $detail->bahan->stok < $detail->jumlah;
                }
            );

            $menu->update([
                'status_stok' => $bahanTidakCukup ? 'Habis' : 'Tersedia',
            ]);
        }
    }
}

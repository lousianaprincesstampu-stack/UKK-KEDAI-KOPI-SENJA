<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Seeder;

class DemoStockSeeder extends Seeder
{
    public function run(): void
    {
        $bahan = Bahan::where('nama_bahan', 'Susu UHT')->first();

        if (!$bahan) {
            return;
        }

        $bahan->update([
            'stok' => 300,
        ]);
    }
}

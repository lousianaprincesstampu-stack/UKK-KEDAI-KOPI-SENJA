<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            [
                'kode_kategori' => 'DRINK',
                'nama_kategori' => 'Minuman',
            ],
            [
                'kode_kategori' => 'FOOD',
                'nama_kategori' => 'Makanan',
            ],
        ];

        foreach ($kategori as $data) {
            DB::table('kategori_menus')->updateOrInsert(
                ['kode_kategori' => $data['kode_kategori']],
                [
                    'nama_kategori' => $data['nama_kategori'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

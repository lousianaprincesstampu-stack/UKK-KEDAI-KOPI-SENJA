<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Menjalankan seeder untuk membuat akun Manajer.
        $this->call([
            ManagerSeeder::class,
        ]);
    }
}

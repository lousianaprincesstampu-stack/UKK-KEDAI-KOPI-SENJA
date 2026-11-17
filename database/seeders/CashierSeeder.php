<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'cashier',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}

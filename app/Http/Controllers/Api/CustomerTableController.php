<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;


class CustomerTableController extends Controller
{
    public function index()
    {
        $tables = CafeTable::select('id', 'nomor_meja')
        ->orderBy('nomor_meja')
        ->get();

        return response()->json([
            'message' => 'Daftar meja berhasil diambil',
            'tables'=>$tables,
        ]);
    }
}

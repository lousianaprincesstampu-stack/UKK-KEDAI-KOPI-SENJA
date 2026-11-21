<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class CustomerMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::where('status', true)
            ->select('id', 'nama_menu', 'kategori', 'harga_jual', 'status_stok')
            ->orderBy('nama_menu')
            ->get();

        return response()->json([
            'message' => 'Daftar menu berhasil diambil.',
            'menus' => $menus,
        ]);
    }

    public function show($id)
    {
        $menu = Menu::where('status', true)
            ->select('id', 'nama_menu', 'kategori', 'harga_jual', 'status_stok')
            ->find($id);

        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail menu berhasil diambil.',
            'menu' => $menu,
        ]);
    }
}

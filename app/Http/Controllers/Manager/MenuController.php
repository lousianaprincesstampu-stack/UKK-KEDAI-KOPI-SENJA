<?php

namespace App\Http\Controllers\Manager;

// Mengimpor Controller utama Laravel.
use App\Http\Controllers\Controller;

// Mengimpor model Menu.
use App\Models\Menu;

class MenuController extends Controller
{
    // Menampilkan seluruh data menu.
    public function index()
    {
        // Mengambil data menu berdasarkan ID terbaru.
        $menus = Menu::orderBy('id', 'desc')->get();

        // Mengirim data menu ke halaman daftar menu.
        return view('manager.menus.index', compact('menus'));
    }

    public function create()
    {
    return view('manager.menus.create');
    }
}

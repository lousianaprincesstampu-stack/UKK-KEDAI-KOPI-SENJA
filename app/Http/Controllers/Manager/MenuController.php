<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
public function index(Request $request)
    {
        // Mengambil kata pencarian dari formulir.
        $cari = $request->input('cari');

        // Membuat query untuk mengambil data menu.
        $query = Menu::query();

        // Jika kolom pencarian diisi, cari berdasarkan nama menu.
        if ($cari) {
            $query->where('nama_menu', 'like', '%' . $cari . '%');
        }

        // Mengurutkan menu berdasarkan ID terbaru.
        $menus = $query->orderBy('id', 'desc')->get();

        // Mengirim data menu dan kata pencarian ke halaman.
        return view('manager.menus.index', compact('menus', 'cari'));
    }

    // Menampilkan formulir tambah menu.
    public function create()
    {
        return view('manager.menus.create');
    }

    // Menyimpan menu baru.
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255|unique:menus,nama_menu',
            'harga_jual' => 'required|numeric|min:1',
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'nama_menu.unique' => 'Nama menu sudah digunakan.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual minimal Rp1.',
        ]);

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'harga_jual' => $request->harga_jual,
            'status' => true,
        ]);

        return redirect()
            ->route('manager.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    // Menampilkan formulir edit menu.
    public function edit(Menu $menu)
    {
        return view('manager.menus.edit', compact('menu'));
    }

    // Memperbarui menu.
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255|unique:menus,nama_menu,' . $menu->id,
            'harga_jual' => 'required|numeric|min:1',
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'nama_menu.unique' => 'Nama menu sudah digunakan.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual minimal Rp1.',
        ]);

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'harga_jual' => $request->harga_jual,
        ]);

        return redirect()
            ->route('manager.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    // Mengubah status Aktif menjadi Nonaktif atau sebaliknya.
    public function toggleStatus(Menu $menu)
    {
        $menu->status = !$menu->status;
        $menu->save();

        return redirect()
            ->route('manager.menus.index')
            ->with('success', 'Status menu berhasil diubah.');
    }
}

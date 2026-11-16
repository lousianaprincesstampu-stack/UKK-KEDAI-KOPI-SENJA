<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');
        $query = Menu::with('recipe.recipeDetails.bahan');

        if ($cari) {
            $query->where('nama_menu', 'like', '%' . $cari . '%');
        }

        //Mengurutkan menu berdasarkan nama.
        $menus = $query->orderBy('nama_menu', 'asc')->get();

        // Menghitung HPP dan margin setiap menu.
        foreach ($menus as $menu) {

        // Nilai awal HPP adalah 0.
        $menu->hpp = 0;

        // Menghitung HPP jika menu sudah memiliki resep.
        if ($menu->recipe) {

            // Menjumlahkan takaran bahan dikali harga per satuan.
            $menu->hpp = $menu->recipe->recipeDetails->sum(function ($detail) {
                return (float) $detail->jumlah
                    * (float) ($detail->bahan->harga_per_satuan ?? 0);
            });
    }

    // Menghitung margin dari harga jual dikurangi HPP.
    $menu->margin = (float) $menu->harga_jual - $menu->hpp;
}

return view('manager.recipes.index', compact('menus', 'cari'));

    }

    public function create()
    {
        //mengambil menu yg belum memiliki resep.
        $menus = Menu::whereDoesntHave('recipe')
            ->orderBy('nama_menu', 'asc')
            ->get();

        // Mengambil daftar bahan yg tersedia.
        $bahans = Bahan::orderBy('nama_bahan', 'asc')->get();

        //Menampilkan halaman formulir tambah resep.
        return view('manager.recipes.create', compact('menus', 'bahans'));
    }
    public function store(Request $request)
    {
        // Memeriksa data yang dikirim dari formulir.
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'bahan_id' => 'required|array|min:1',
            'bahan_id.*' => 'required|distinct|exists:bahans,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|gt:0',
        ], [
            'menu_id.required' => 'Menu wajib dipilih.',
            'menu_id.exists' => 'Menu yang dipilih tidak ditemukan.',
            'bahan_id.required' => 'Minimal satu bahan harus dipilih.',
            'bahan_id.min' => 'Minimal satu bahan harus dipilih.',
            'bahan_id.*.distinct' => 'Bahan yang sama tidak boleh dipilih dua kali.',
            'bahan_id.*.exists' => 'Bahan yang dipilih tidak ditemukan.',
            'jumlah.required' => 'Jumlah bahan wajib diisi.',
            'jumlah.*.numeric' => 'Jumlah bahan harus berupa angka.',
            'jumlah.*.gt' => 'Jumlah bahan harus lebih dari 0.',
        ]);

        // Memastikan jumlah bahan dan takarannya memiliki pasangan.
        if (count($request->bahan_id) !== count($request->jumlah)) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => 'Data bahan dan jumlah tidak sesuai.']);
        }

        // Memeriksa apakah menu sudah memiliki resep.
        $menu = Menu::findOrFail($request->menu_id);

        if ($menu->recipe()->exists()) {
            return back()
                ->withInput()
                ->withErrors(['menu_id' => 'Menu tersebut sudah memiliki resep.']);
        }

        // Menyimpan resep dan seluruh detail bahan dalam satu transaksi.
        DB::transaction(function () use ($request) {

            $recipe = Recipe::create([
                'menu_id' => $request->menu_id,
            ]);

            // Menyimpan setiap bahan dan jumlah pemakaiannya.
            foreach ($request->bahan_id as $index => $bahanId) {
                RecipeDetail::create([
                    'recipe_id' => $recipe->id,
                    'bahan_id' => $bahanId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }
        });

        return redirect()
            ->route('manager.recipes.index')
            ->with('success', 'Resep berhasil ditambahkan.');
    }

    public function edit(Recipe $recipe)
    {
        // Mengambil resep beserta komposisi bahan yang sudah tersimpan.
        $recipe->load('recipeDetails.bahan');

        // Mengambil seluruh bahan untuk pilihan komposisi.
        $bahans = Bahan::orderBy('nama_bahan', 'asc')->get();

        // Menampilkan halaman edit resep.
        return view('manager.recipes.edit', compact('recipe', 'bahans'));
    }
    public function update(Request $request, Recipe $recipe)
    {
        // Memeriksa apakah bahan dan jumlah yang dikirim valid.
        $request->validate([
            'bahan_id' => 'required|array|min:1',
            'bahan_id.*' => 'required|distinct|exists:bahans,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|gt:0',
        ]);

        // Memastikan jumlah pilihan bahan dan jumlah takaran sesuai.
        if (count($request->bahan_id) !== count($request->jumlah)) {
            return back()->withInput()->withErrors([
                'jumlah' => 'Data bahan dan jumlah tidak sesuai.'
            ]);
        }

        // Memperbarui seluruh komposisi dalam satu transaksi database.
        DB::transaction(function () use ($request, $recipe) {

            // Menghapus komposisi lama dari resep ini.
            $recipe->recipeDetails()->delete();

            // Menyimpan kembali komposisi bahan yang baru.
            foreach ($request->bahan_id as $index => $bahanId) {
                RecipeDetail::create([
                    'recipe_id' => $recipe->id,
                    'bahan_id' => $bahanId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }
        });
        return redirect()->route('manager.recipes.index')
            ->with('success', 'Resep berhasil diperbarui.');
    }

    public function destroy(Recipe $recipe)
    {
        // Menghapus resep beeserta seluruh komposisi bahannya.
        DB::transaction(function () use ($recipe) {

            // Menghapus seluruh detail bahan dari resep.
            $recipe->recipeDetails()->delete();

            $recipe->delete();
        });

        return redirect()
            ->route('manager.recipes.index')
            ->with('success', 'Resep berhasil dihapus.');
    }
}

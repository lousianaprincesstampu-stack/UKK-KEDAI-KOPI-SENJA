<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;
use App\Services\LayananUkkService;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');
        $query = Bahan::query();

        if ($cari) {
            $query->where('nama_bahan', 'like', '%' . $cari . '%');
        }

        $bahans = $query->orderBy('nama_bahan', 'asc')->get();
        return view('manager.bahans.index', compact('bahans', 'cari'));
    }
    public function create()
    {
        return view('manager.bahans.create');
    }

    //Menyimpan bahan baru ke database.
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'satuan_dasar' => 'required|in:g,ml,pcs',
            'stok' => 'nullable|numeric|min:0',
            'stok_minimum' => 'nullable|numeric|min:0',
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan_dasar.required' => 'Satuan dasar wajib dipilih.',
            'satuan_dasar.in' => 'Satuan harus berupa gram, ml, atau pcs.',
            'stok.numeric' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
            'stok_minimum.numeric' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
        ]);
        $namaBahan = preg_replace('/\s+/', ' ', trim($request->nama_bahan));
        $namaNormal = mb_strtolower($namaBahan);
        $duplikat = Bahan::all()->contains(function ($bahan) use ($namaNormal) {
            $namaLama = mb_strtolower(
                preg_replace('/\s+/', ' ', trim($bahan->nama_bahan))
            );

            return $namaLama === $namaNormal;
        });

        if ($duplikat) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_bahan' => 'Nama bahan sudah digunakan.',
                ]);
        }
        Bahan::create([
            'nama_bahan' => $namaBahan,
            'kategori' => $request->kategori,
            'satuan_dasar' => $request->satuan_dasar,
            'stok' => $request->stok ?? 0,
            'stok_minimum' => $request->stok_minimum ?? 0,
        ]);

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function updateMinimumStock(Request $request, Bahan $bahan)
    {
        $request->validate([
            'stok_minimum' => 'required|numeric|min:0',
        ], [
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.numeric' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
        ]);
        $bahan->update([
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Stok minimum berhasil diperbarui.');
    }

    public function edit(Bahan $bahan)
    {
        return view('manager.bahans.edit', compact('bahan'));
    }


    public function update(Request $request, Bahan $bahan)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'satuan_dasar' => 'required|in:g,ml,pcs',
            'stok' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'harga_per_satuan' => 'nullable|numeric|min:0',
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan_dasar.required' => 'Satuan dasar wajib dipilih.',
            'satuan_dasar.in' => 'Satuan harus berupa g, ml, atau pcs.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.numeric' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.numeric' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'harga_per_satuan.numeric' => 'Harga harus berupa angka.',
            'harga_per_satuan.min' => 'Harga tidak boleh kurang dari 0.',
        ]);

        $namaBahan = preg_replace('/\s+/', ' ', trim($request->nama_bahan));
        $namaNormal = mb_strtolower($namaBahan);

        $duplikat = Bahan::where('id', '!=', $bahan->id)
            ->get()
            ->contains(function ($item) use ($namaNormal) {
                $namaLama = mb_strtolower(
                    preg_replace('/\s+/', ' ', trim($item->nama_bahan))
                );

                return $namaLama === $namaNormal;
            });

        if ($duplikat) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_bahan' => 'Nama bahan sudah digunakan.',
                ]);
        }

        $bahan->update([
            'nama_bahan' => $namaBahan,
            'kategori' => $request->kategori,
            'satuan_dasar' => $request->satuan_dasar,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum,
            'harga_per_satuan' => $request->harga_per_satuan,
        ]);

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    public function updateHarga(LayananUkkService $layananUkkService)
    {
        $berhasil = $layananUkkService->sinkronkanHargaBahan();

        if (!$berhasil) {
            return redirect()
                ->route('manager.bahans.index')
                ->with('error', 'Gagal memperbarui harga bahan dari API.');
        }

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Harga bahan berhasil diperbarui dari API.');
    }

    public function destroy(Bahan $bahan)
    {
        $digunakanDiResep = $bahan->recipeDetails()->exists();

        $memilikiStokMasuk = $bahan->stockEntries()->exists();

        $memilikiRiwayatStok = $bahan->stockMovements()->exists();

        $memilikiHargaSupplier = $bahan->supplierPrices()->exists();

        if (
            $digunakanDiResep ||
            $memilikiStokMasuk ||
            $memilikiRiwayatStok ||
            $memilikiHargaSupplier
        ) {
            return redirect()
                ->route('manager.bahans.index')
                ->with('error', 'Bahan tidak dapat dihapus karena masih memiliki data terkait.');
        }
        $bahan->delete();

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Data bahan berhasil dihapus.');
    }
}

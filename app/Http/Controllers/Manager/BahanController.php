<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');
        $query = Bahan::query();

        if ($cari) {
            $query->where('nama_bahan', 'like', '%' . $cari . '%');
        }

        // Mengurutkan bahan berdasarkan nama secara alfabet.
        $bahans = $query->orderBy('nama_bahan', 'asc')->get();

        // Mengirim data bahan dan kata pencarian ke halaman.
        return view('manager.bahans.index', compact('bahans', 'cari'));
    }

    // Menampilkan formulir tambah bahan.
    public function create()
    {
        return view('manager.bahans.create');
    }

    // Menyimpan bahan baru ke database.
    public function store(Request $request)
    {
        // Memeriksa data yang dikirim dari formulir.
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

        // Merapikan spasi berlebihan pada nama bahan.
        $namaBahan = preg_replace('/\s+/', ' ', trim($request->nama_bahan));

        // Mengubah nama menjadi huruf kecil untuk pemeriksaan duplikat.
        $namaNormal = mb_strtolower($namaBahan);

        // Memeriksa apakah nama bahan sudah pernah digunakan.
        $duplikat = Bahan::all()->contains(function ($bahan) use ($namaNormal) {
            $namaLama = mb_strtolower(
                preg_replace('/\s+/', ' ', trim($bahan->nama_bahan))
            );

            return $namaLama === $namaNormal;
        });

        // Menolak penyimpanan jika nama bahan sudah ada.
        if ($duplikat) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_bahan' => 'Nama bahan sudah digunakan.',
                ]);
        }

        // Menyimpan bahan baru dengan stok default 0 jika tidak diisi.
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

    // Memperbarui batas minimum stok bahan.
    public function updateMinimumStock(Request $request, Bahan $bahan)
    {
        // Memeriksa nilai stok minimum.
        $request->validate([
            'stok_minimum' => 'required|numeric|min:0',
        ], [
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.numeric' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
        ]);

        // Menyimpan batas minimum stok.
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
        // Memeriksa data dari formulir.
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'satuan_dasar' => 'required|in:g,ml,pcs',
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan_dasar.required' => 'Satuan dasar wajib dipilih.',
            'satuan_dasar.in' => 'Satuan harus berupa g, ml, atau pcs.',
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
        ]);

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    // Menghapus data bahan.
    public function destroy(Bahan $bahan)
    {
        // Memmeriksa apakah bahan masih digunakan dlm detail resep.
        $digunakanDiResep = $bahan->recipeDetails()->exists();

        // Memeriksa apakah bahan memiliki riwayat stok masuk.
        $memilikiStokMasuk = $bahan->stockEntries()->exists();

        //Memeriksa apakah bahan memiliki riwayat pergerakan stok.
        $memilikiRiwayatStok = $bahan->stockMovements()->exists();

        // Memeriksa apakah bahan memiliki riwayat harga supplier.
        $memilikiHargaSupplier = $bahan->supplierPrices()->exists();

        //mencegah penghapusan jika bahan masih memiliki data terkait.
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

        // Menghapus bahan jika tidak memiliki data terkait.
        $bahan->delete();

        return redirect()
            ->route('manager.bahans.index')
            ->with('success', 'Data bahan berhasil dihapus.');
    }
}

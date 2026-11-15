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

        // Merapikan spasi pada nama bahan.
        $namaBahan = preg_replace('/\s+/', ' ', trim($request->nama_bahan));

        // Membandingkan nama tanpa membedakan huruf besar dan kecil.
        $namaNormal = mb_strtolower($namaBahan);

        // Memeriksa apakah nama bahan sudah pernah digunakan.
        $duplikat = Bahan::all()->contains(function ($bahan) use ($namaNormal) {
            $namaLama = mb_strtolower(
                preg_replace('/\s+/', ' ', trim($bahan->nama_bahan))
            );

            return $namaLama === $namaNormal;
        });

        // Mengembalikan formulir jika nama bahan sudah ada.
        if ($duplikat) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_bahan' => 'Nama bahan sudah digunakan.',
                ]);
        }

        // Menyimpan bahan baru dengan stok dan stok minimum default 0.
        Bahan::create([
            'nama_bahan' => $namaBahan,
            'kategori' => $request->kategori,
            'satuan_dasar' => $request->satuan_dasar,
            'stok' => $request->stok ?? 0,
            'stok_minimum' => $request->stok_minimum ?? 0,
        ]);

        // Kembali ke halaman Data Bahan setelah berhasil disimpan.
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

            // Menyimpan batas minimum stok ke database.
            $bahan->update([
                'stok_minimum' => $request->stok_minimum,
            ]);

            // Kembali ke halaman data Bahan.
            return redirect()
                ->route('manager.bahans.index')
                ->with('success', 'Stok minimum berhasil diperbarui.');
        }
}
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\StockEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockEntryController extends Controller
{
    public function index()
    {
        // Mengambil data stok masuk beserta nama bahan dan pengguna.
        $stockEntries = StockEntry::with(['bahan', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Menampilkan halaman stok masuk.
        return view('manager.stock-entries.index', compact('stockEntries'));
    }

    public function create()
    {
        // Mengambil seluruh bahan untuk pilihan formulir.
        $bahans = Bahan::orderBy('nama_bahan', 'asc')->get();

        // Menampilkan halaman tambah stok masuk.
        return view('manager.stock-entries.create', compact('bahans'));
    }

    public function store(Request $request)
    {
        // Memvalidasi data yang dikirim dari formulir.
        $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'jumlah' => 'required|numeric|gt:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'bahan_id.required' => 'Bahan wajib dipilih.',
            'bahan_id.exists' => 'Bahan tidak ditemukan.',
            'jumlah.required' => 'Jumlah stok masuk wajib diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.gt' => 'Jumlah harus lebih dari 0.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        // Menyimpan transaksi dan menambahkan stok secara bersamaan.
        DB::transaction(function () use ($request) {

            // Mengunci data bahan agar perubahan stok aman.
            $bahan = Bahan::where('id', $request->bahan_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Mencatat riwayat stok masuk.
            StockEntry::create([
                'bahan_id' => $bahan->id,
                'jumlah' => $request->jumlah,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'user_id' => Auth::id(),
            ]);
            $bahan->increment('stok', $request->jumlah);
        });
        return redirect()->route('manager.stock-entries.index')
            ->with('success', 'Stok masuk berhasil dicatat.');
    }
}

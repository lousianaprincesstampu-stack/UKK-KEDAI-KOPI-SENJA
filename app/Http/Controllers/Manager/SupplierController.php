<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');
        $query = Supplier::query();

        if ($cari) {
            $query->where('nama_supplier', 'like', '%' . $cari . '%');
        }

        // Mengurutkan supplier dari yang terbaru.
        $suppliers = $query->orderBy('id', 'desc')->get();

        // Mengirim data supplier dan pencarian ke halaman.
        return view('manager.suppliers.index', compact('suppliers', 'cari'));
    }

    public function create()
    {
         return view('manager.suppliers.create');
    }

    public function store(Request $request)
    {
        // Memeriksa data yang dikirim dari formulir.
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        //Mmenyimpan supplier dengan status aktif.
        $validated['status'] = true;

        Supplier::create($validated);

        // Kembali ke halaman daftar supplier.
        return redirect()->route('manager.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
         // Mengambil data supplier berdasarkan ID.
        $supplier = Supplier::findOrFail($id);

        return view('manager.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, string $id)
    {
          // Memeriksa data yang dikirim dari formulir.
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
    ]);

    $supplier = Supplier::findOrFail($id);

    $supplier->update($validated);

    return redirect()->route('manager.suppliers.index')
        ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        // Mencegah penghapusan jika masih memiliki riwayat harga.
        if ($supplier->supplierPrices()->exists()) {
            return redirect()->route('manager.suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki riwayat harga.');
        }

        $supplier->delete();

        return redirect()->route('manager.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportCsvController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);
        $handle = fopen($request->file('file_csv')->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json([
                'message' => 'File CSV gagal dibaca.',
            ], 422);
        }

        $header = fgetcsv($handle, 0, ',', '"', '');

        // Menghapus BOM jika ada.
        if (!empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $headerWajib = [
            'kode',
            'nama_bahan',
            'stok_awal',
            'satuan',
            'harga_per_satuan',
        ];

        if ($header !== $headerWajib) {
            fclose($handle);

            return response()->json([
                'message' => 'Format CSV tidak sesuai. Gunakan kolom: kode,nama_bahan,stok_awal,satuan,harga_per_satuan.',
            ], 422);
        }

        $dataCsv = [];
        $errors = [];
        $nomorBaris = 1;

        while (($baris = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $nomorBaris++;

            if ($baris === [null]) {
                continue;
            }

            if (count($baris) !== 5) {
                $errors[] = "Baris {$nomorBaris}: jumlah kolom tidak sesuai.";
                continue;
            }

            $kode = trim($baris[0]);
            $nama = trim($baris[1]);
            $stokAwal = trim($baris[2]);
            $satuanAsli = strtolower(trim($baris[3]));
            $hargaCsv = trim($baris[4]);

            if ($kode === '' || $nama === '') {
                $errors[] = "Baris {$nomorBaris}: kode atau nama bahan kosong.";
                continue;
            }

            if (
                !is_numeric($stokAwal) ||
                !is_numeric($hargaCsv) ||
                (float) $stokAwal < 0 ||
                (float) $hargaCsv < 0
            ) {
                $errors[] = "Baris {$nomorBaris}: stok atau harga tidak valid.";
                continue;
            }

            // Menentukan konversi ke satuan dasar.
            $faktor = match ($satuanAsli) {
                'kg', 'liter' => 1000,
                'g', 'ml', 'pcs' => 1,
                default => null,
            };

            if ($faktor === null) {
                $errors[] = "Baris {$nomorBaris}: satuan {$satuanAsli} tidak dikenali.";
                continue;
            }

            // Menentukan satuan dasar bahan.
            $satuanDasar = match ($satuanAsli) {
                'kg', 'g' => 'g',
                'liter', 'ml' => 'ml',
                'pcs' => 'pcs',
            };

            $stokDasar = (float) $stokAwal * $faktor;
            $hargaDasar = (float) $hargaCsv / $faktor;

            $dataCsv[] = [
                'kode_bahan' => $kode,
                'nama_bahan' => $nama,
                'satuan_dasar' => $satuanDasar,
                'stok' => $stokDasar,
                'harga_per_satuan' => $hargaDasar,
            ];
        }

        fclose($handle);

        if (!empty($errors)) {
            return response()->json([
                'message' => implode("\n", $errors),
            ], 422);
        }

        $jumlahDiperbarui = 0;
        $jumlahDilewati = 0;

        DB::transaction(function () use (
            $dataCsv,
            &$jumlahDiperbarui,
            &$jumlahDilewati
        ) {
            foreach ($dataCsv as $data) {

                $bahan = Bahan::whereRaw(
                    'LOWER(TRIM(nama_bahan)) = ?',
                    [mb_strtolower($data['nama_bahan'])]
                )->first();

                if (!$bahan) {
                    $jumlahDilewati++;
                    continue;
                }

                if ($bahan->satuan_dasar !== $data['satuan_dasar']) {
                    throw new \RuntimeException(
                        "Satuan bahan {$data['nama_bahan']} tidak cocok dengan database."
                    );
                }

                $bahan->update([
                    'kode_bahan' => $data['kode_bahan'],
                    'stok' => $data['stok'],
                ]);

                $jumlahDiperbarui++;
            }
        });

        return response()->json([
            'message' => "Import selesai. {$jumlahDiperbarui} bahan diperbarui dan {$jumlahDilewati} bahan dilewati karena tidak ditemukan.",
        ]);
    }
}

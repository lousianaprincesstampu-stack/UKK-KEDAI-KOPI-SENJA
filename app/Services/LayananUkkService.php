<?php

namespace App\Services;

use App\Models\Bahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LayananUkkService
{

    public function ambilHargaBahan()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.layanan_ukk.api_key'),
            ])
            ->timeout(10)
            ->get(
                rtrim(config('services.layanan_ukk.url'), '/') . '/harga-bahan'
            );

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {

            return null;
        }
    }

    public function sinkronkanHargaBahan()
    {

        $response = $this->ambilHargaBahan();

        // Memeriksa apakah respons API valid.
        if (
            !$response ||
            !isset($response['data']) ||
            !is_array($response['data']) ||
            !isset($response['diperbarui'])
        ) {
            return false;
        }

        $tanggal = $response['diperbarui'];

        try {

            DB::transaction(function () use ($response, $tanggal) {

                foreach ($response['data'] as $item) { // Mengulang semua data bahan dari API.

                    // Memastikan semua informasi penting tersedia.
                    if (
                        empty($item['kode']) ||
                        empty($item['nama']) ||
                        empty($item['satuan_dasar']) ||
                        !isset($item['harga_per_satuan']) ||
                        !is_numeric($item['harga_per_satuan'])
                    ) {
                        continue;
                    }

                    // Mencari bahan berdasarkan kode API atau nama.
                    $bahan = Bahan::where('kode_api', $item['kode'])
                        ->orWhere('nama_bahan', $item['nama'])
                        ->first();

                    // Membuat bahan baru jika belum ditemukan.
                    if (!$bahan) {
                        $bahan = new Bahan();
                    }

                    // Memperbarui informasi bahan dari API.
                    $bahan->kode_api = $item['kode'];
                    $bahan->nama_bahan = $item['nama'];
                    $bahan->satuan_dasar = $item['satuan_dasar'];
                    $bahan->harga_per_satuan = $item['harga_per_satuan'];
                    $bahan->harga_diperbarui = $tanggal;

                    // Menyimpan data ke database. Stok dan kategori yang sudah ada tidak diubah.
                    $bahan->save();
                }
            });

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
}

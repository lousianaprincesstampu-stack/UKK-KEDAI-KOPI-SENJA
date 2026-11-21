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

        if (
            !$response ||
            !isset($response['data']) ||
            !is_array($response['data']) ||
            !isset($response['diperbarui'])
        ) {
            return false;
        }

        $tanggal = $response['diperbarui'];
        $jumlahBerhasil = 0;

        try {
            DB::transaction(function () use ($response, $tanggal, &$jumlahBerhasil) {
                foreach ($response['data'] as $item) {
                    if (
                        empty($item['kode']) ||
                        empty($item['nama']) ||
                        empty($item['satuan_dasar']) ||
                        !isset($item['harga_per_satuan']) ||
                        !is_numeric($item['harga_per_satuan'])
                    ) {
                        continue;
                    }

                    $bahan = Bahan::firstOrNew([
                        'kode_api' => $item['kode'],
                    ]);

                    $bahan->nama_bahan = $item['nama'];
                    $bahan->satuan_dasar = $item['satuan_dasar'];
                    $bahan->harga_per_satuan = $item['harga_per_satuan'];
                    $bahan->harga_diperbarui = $tanggal;

                    if (!$bahan->exists) {
                        $bahan->kategori = null;
                        $bahan->stok = 0;
                        $bahan->stok_minimum = 0;
                    }

                    $bahan->save();
                    $jumlahBerhasil++;
                }
            });

            return $jumlahBerhasil > 0;

        } catch (\Exception $e) {
            return false;
        }
    }

}

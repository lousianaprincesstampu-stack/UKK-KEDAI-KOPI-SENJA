<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\MenuStockService;
use RuntimeException;

class ImportCsvService
{
    public function importSemua(Request $request): array
    {
        $request->validate([
            'file_bahan' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'file_menu' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'file_resep' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $dataBahan = $this->bacaCsv(
            $request->file('file_bahan'),
            ['kode', 'nama_bahan', 'stok_awal', 'satuan', 'harga_per_satuan'],
            'bahan'
        );

        $dataMenu = $this->bacaCsv(
            $request->file('file_menu'),
            ['kode', 'nama_menu', 'kategori', 'harga_jual'],
            'menu'
        );

        $dataResep = $this->bacaCsv(
            $request->file('file_resep'),
            ['kode_menu', 'nama_bahan', 'takaran', 'satuan'],
            'resep'
        );

        $hasilBahan = $this->importBahan($dataBahan);
        $hasilMenu = $this->importMenu($dataMenu);
        $hasilResep = $this->importResep($dataResep);

        app(\App\Services\MenuStockService::class)->perbaruiSemuaStatus();


        return [
            'message' => 'Proses import semua data selesai.',
            'bahan' => $hasilBahan,
            'menu' => $hasilMenu,
            'resep' => $hasilResep,
        ];
    }


    public function importBahanFile(Request $request): array
    {
        $request->validate([
            'file_bahan' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $data = $this->bacaCsv(
            $request->file('file_bahan'),
            ['kode', 'nama_bahan', 'stok_awal', 'satuan', 'harga_per_satuan'],
            'bahan'
        );

        $hasil = $this->importBahan($data);

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return [
            'message' => 'Proses import bahan selesai.',
            'bahan' => $hasil,
        ];
    }

    public function importMenuFile(Request $request): array
    {
        $request->validate([
            'file_menu' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $data = $this->bacaCsv(
            $request->file('file_menu'),
            ['kode', 'nama_menu', 'kategori', 'harga_jual'],
            'menu'
        );

        $hasil = $this->importMenu($data);

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return [
            'message' => 'Proses import menu selesai.',
            'menu' => $hasil,
        ];
    }

    public function importResepFile(Request $request): array
    {
        $request->validate([
            'file_resep' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $data = $this->bacaCsv(
            $request->file('file_resep'),
            ['kode_menu', 'nama_bahan', 'takaran', 'satuan'],
            'resep'
        );

        $hasil = $this->importResep($data);

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return [
            'message' => 'Proses import resep selesai.',
            'resep' => $hasil,
        ];
    }


    private function bacaCsv($file, array $headerWajib, string $jenis): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw new RuntimeException("File {$jenis}.csv gagal dibaca.");
        }

        try {
            $header = fgetcsv($handle, 0, ',', '"', '');

            if (!$header) {
                throw new RuntimeException("File {$jenis}.csv kosong.");
            }

           $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            $header = array_map('trim', $header);

            if ($header !== $headerWajib) {
                throw new RuntimeException(
                    "Format {$jenis}.csv tidak sesuai. Header yang diperlukan: " .
                    implode(',', $headerWajib)
                );
            }

            $data = [];
            $nomorBaris = 1;

            while (($baris = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                $nomorBaris++;

                if ($baris === [null] || (count($baris) === 1 && trim($baris[0] ?? '') === '')) {
                    continue;
                }

                if (count($baris) !== count($headerWajib)) {
                    $data[] = [
                        '_nomor_baris' => $nomorBaris,
                        '_error' => 'Jumlah kolom tidak sesuai.',
                    ];

                    continue;
                }

                $data[] = array_merge(
                    array_combine($headerWajib, array_map('trim', $baris)),
                    ['_nomor_baris' => $nomorBaris]
                );
            }

            return $data;
        } finally {
            fclose($handle);
        }
    }

    
    
    private function importBahan(array $data): array
    {
        $berhasil = 0;
        $dilewati = 0;
        $details = [];

        $semuaBahan = Bahan::all();

        $kodeDiproses = [];


        foreach ($data as $baris) {
            $nomor = $baris['_nomor_baris'];

            if (isset($baris['_error'])) {
                $dilewati++;
                $details[] = "Baris {$nomor}: {$baris['_error']}";
                continue;
            }

            $kode = $baris['kode'];
            $nama = $baris['nama_bahan'];
            $stok = $baris['stok_awal'];
            $satuan = strtolower($baris['satuan']);
            $harga = $baris['harga_per_satuan'];

            if (
                $kode === '' ||
                $nama === '' ||
                !is_numeric($stok) ||
                !is_numeric($harga) ||
                (float) $stok < 0 ||
                (float) $harga < 0
            ) {
                $dilewati++;
                $details[] = "Baris {$nomor}: data bahan tidak valid.";
                continue;
            }

            if (in_array($kode, $kodeDiproses, true)) {
                $dilewati++;
                $details[] = "Baris {$nomor}: kode {$kode} duplikat dalam CSV.";
                continue;
            }

            $faktor = $this->faktorSatuan($satuan);

            if ($faktor === null) {
                $dilewati++;
                $details[] = "Baris {$nomor}: satuan {$satuan} tidak dikenali.";
                continue;
            }

            $satuanDasar = $this->satuanDasar($satuan);
            $namaNormal = $this->normalisasiNama($nama);

            $bahanSama = $semuaBahan->first(
                fn ($bahan) =>
                    $this->normalisasiNama($bahan->nama_bahan) === $namaNormal
            );

            $pemilikKode = $semuaBahan->first(
                fn ($bahan) => $bahan->kode_bahan === $kode
            );

            if (
                $pemilikKode &&
                (!$bahanSama || $pemilikKode->id !== $bahanSama->id)
            ) {
                $dilewati++;
                $details[] = "Baris {$nomor}: kode {$kode} sudah digunakan bahan lain.";
                continue;
            }

            if ($bahanSama) {
                if ($bahanSama->satuan_dasar !== $satuanDasar) {
                    $dilewati++;
                    $details[] = "Baris {$nomor}: satuan dasar {$nama} tidak sesuai dengan database.";
                    continue;
                }

                $bahanSama->update([
                    'kode_bahan' => $kode,
                ]);

                $kodeDiproses[] = $kode;
                $berhasil++;

                $details[] = "Baris {$nomor}: kode bahan {$nama} berhasil diperbarui. Harga tetap mengikuti API.";
                continue;
            }

            $bahanMirip = $this->cariBahanCocok($nama, $semuaBahan);

            if ($bahanMirip) {
                $dilewati++;
                $details[] = "Baris {$nomor}: {$nama} kemungkinan duplikat dari {$bahanMirip->nama_bahan}.";
                continue;
            }

            try {
                $bahanBaru = Bahan::create([
                    'kode_bahan' => $kode,
                    'nama_bahan' => $nama,
                    'kategori' => null,
                    'satuan_dasar' => $satuanDasar,
                    'stok' => round((float) $stok * $faktor, 3),
                    'stok_minimum' => 0,
                ]);

                $semuaBahan->push($bahanBaru);

                $kodeDiproses[] = $kode;
                $berhasil++;

                $details[] = "Baris {$nomor}: {$nama} berhasil ditambahkan.";
            } catch (\Illuminate\Database\QueryException $e) {
                $dilewati++;
                $details[] = "Baris {$nomor}: {$nama} gagal disimpan karena data duplikat.";
            }
        }

        return compact('berhasil', 'dilewati', 'details');
    }


    private function importMenu(array $data): array
    {
        $berhasil = 0;
        $dilewati = 0;
        $details = [];

        foreach ($data as $baris) {
            $nomor = $baris['_nomor_baris'];

            if (isset($baris['_error'])) {
                $dilewati++;
                $details[] = "Baris {$nomor}: {$baris['_error']}";
                continue;
            }

            $kode = $baris['kode'];
            $nama = $baris['nama_menu'];
            $kategori = $baris['kategori'];
            $harga = $baris['harga_jual'];

            if (
                $kode === '' ||
                $nama === '' ||
                $kategori === '' ||
                !is_numeric($harga) ||
                (float) $harga < 0
            ) {
                $dilewati++;
                $details[] = "Baris {$nomor}: data menu tidak valid.";
                continue;
            }

            if (
                Menu::where('kode_menu', $kode)->exists() ||
                Menu::where('nama_menu', $nama)->exists()
            ) {
                $dilewati++;
                $details[] = "Baris {$nomor}: menu {$nama} sudah tersedia.";
                continue;
            }

            $kategoriAda = DB::table('kategori_menus')
                ->where('nama_kategori', $kategori)
                ->exists();

            if (!$kategoriAda) {
                $dilewati++;
                $details[] = "Baris {$nomor}: kategori {$kategori} tidak ditemukan.";
                continue;
            }

            try {
                Menu::create([
                    'kode_menu' => $kode,
                    'nama_menu' => $nama,
                    'kategori' => $kategori,
                    'harga_jual' => $harga,
                    'status' => true,
                ]);

                $berhasil++;
                $details[] = "Baris {$nomor}: menu {$nama} berhasil diimpor.";
            } catch (\Illuminate\Database\QueryException $e) {
                $dilewati++;
                $details[] = "Baris {$nomor}: menu {$nama} gagal disimpan karena data duplikat.";
            }
        }

        return compact('berhasil', 'dilewati', 'details');
    }


    private function importResep(array $data): array
    {
        $berhasil = 0;
        $dilewati = 0;
        $details = [];
        $kelompokResep = [];

        foreach ($data as $baris) {
            if (isset($baris['_error'])) {
                $dilewati++;
                $details[] = "Baris {$baris['_nomor_baris']}: {$baris['_error']}";
                continue;
            }

            $kodeMenu = $baris['kode_menu'];

            if ($kodeMenu === '') {
                $dilewati++;
                $details[] = "Baris {$baris['_nomor_baris']}: kode menu kosong.";
                continue;
            }

            $kelompokResep[$kodeMenu][] = $baris;
        }

        foreach ($kelompokResep as $kodeMenu => $barisResep) {
            $menu = Menu::where('kode_menu', $kodeMenu)->first();

            if (!$menu) {
                $dilewati += count($barisResep);
                $details[] = "Resep {$kodeMenu}: menu tidak ditemukan.";
                continue;
            }

            if (Recipe::where('menu_id', $menu->id)->exists()) {
                $dilewati += count($barisResep);
                $details[] = "Resep {$kodeMenu}: resep sudah tersedia.";
                continue;
            }

            $semuaBahan = Bahan::all();
            $rincian = [];
            $resepValid = true;
            $jumlahBahanTidakTerdaftar = 0;
            $alasan = '';

            foreach ($barisResep as $baris) {
                $namaBahan = $baris['nama_bahan'];
                $takaran = $baris['takaran'];
                $satuan = strtolower($baris['satuan']);

                if ($namaBahan === '' || !is_numeric($takaran) || (float) $takaran <= 0) {
                    $resepValid = false;
                    $alasan = 'nama bahan atau takaran tidak valid.';
                    break;
                }

                $bahan = $this->cariBahanCocok($namaBahan, $semuaBahan);

                if (!$bahan) {
                    $dilewati++;
                    $jumlahBahanTidakTerdaftar++;

                    $details[] = "Baris {$baris['_nomor_baris']}: bahan {$namaBahan} tidak ditemukan dan dilewati.";
                    continue;
                }

                $jumlah = $this->konversiTakaran(
                    (float) $takaran,
                    $satuan,
                    $bahan->satuan_dasar
                );

                if ($jumlah === null) {
                    $resepValid = false;
                    $alasan = "satuan {$satuan} tidak sesuai dengan bahan {$bahan->nama_bahan}.";
                    break;
                }

                if (isset($rincian[$bahan->id])) {
                    $rincian[$bahan->id] += $jumlah;
                } else {
                    $rincian[$bahan->id] = $jumlah;
                }
            }

            if (!$resepValid) {
                $dilewati += count($barisResep) - $jumlahBahanTidakTerdaftar;
                $details[] = "Resep {$kodeMenu}: {$alasan} Seluruh resep dilewati.";
                continue;
            }

            if (empty($rincian)) {
                $details[] = "Resep {$kodeMenu}: tidak dibuat karena tidak ada bahan terdaftar.";
                continue;
            }

            try {
                DB::transaction(function () use ($menu, $rincian) {
                    $resep = Recipe::create([
                        'menu_id' => $menu->id,
                    ]);

                    foreach ($rincian as $bahanId => $jumlah) {
                        RecipeDetail::create([
                            'recipe_id' => $resep->id,
                            'bahan_id' => $bahanId,
                            'jumlah' => round($jumlah, 3),
                        ]);
                    }
                });

                $berhasil++;
                $details[] = "Resep {$kodeMenu}: berhasil diimpor.";
            } catch (\Throwable $e) {
                $dilewati += count($barisResep) - $jumlahBahanTidakTerdaftar;
                $details[] = "Resep {$kodeMenu}: gagal disimpan.";
            }
        }

        return compact('berhasil', 'dilewati', 'details');
    }


    private function cariBahanCocok(string $nama, $semuaBahan): ?Bahan
    {
        $namaNormal = $this->normalisasiNama($nama);

        foreach ($semuaBahan as $bahan) {
            if ($this->normalisasiNama($bahan->nama_bahan) === $namaNormal) {
                return $bahan;
            }
        }

        $kandidat = [];

        foreach ($semuaBahan as $bahan) {
            similar_text(
                $namaNormal,
                $this->normalisasiNama($bahan->nama_bahan),
                $persentase
            );

            if ($persentase >= 88) {
                $kandidat[] = [
                    'bahan' => $bahan,
                    'persentase' => $persentase,
                ];
            }
        }

        usort(
            $kandidat,
            fn ($a, $b) => $b['persentase'] <=> $a['persentase']
        );

        if (empty($kandidat)) {
            return null;
        }

        if (
            isset($kandidat[1]) &&
            $kandidat[0]['persentase'] - $kandidat[1]['persentase'] < 5
        ) {
            return null;
        }

        return $kandidat[0]['bahan'];
    }

    private function faktorSatuan(string $satuan): ?int
    {
        return match ($satuan) {
            'kg', 'liter' => 1000,
            'g', 'ml', 'pcs' => 1,
            default => null,
        };
    }

    private function satuanDasar(string $satuan): string
    {
        return match ($satuan) {
            'kg', 'g' => 'g',
            'liter', 'ml' => 'ml',
            'pcs' => 'pcs',
        };
    }

    private function konversiTakaran(
        float $takaran,
        string $satuan,
        string $satuanDasar
    ): ?float {
        $satuan = strtolower($satuan);

        if ($satuan === $satuanDasar) {
            return $takaran;
        }

        if ($satuan === 'kg' && $satuanDasar === 'g') {
            return $takaran * 1000;
        }

        if ($satuan === 'liter' && $satuanDasar === 'ml') {
            return $takaran * 1000;
        }

        return null;
    }

    private function normalisasiNama(string $nama): string
    {
        $nama = mb_strtolower(trim($nama));

        return preg_replace('/\s+/', ' ', $nama);
    }
}

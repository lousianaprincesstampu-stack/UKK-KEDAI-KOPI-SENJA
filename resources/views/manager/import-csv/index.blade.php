@extends('manager.layouts.app')
@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">Import CSV</h4>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="border border-2 border-secondary border-opacity-25 rounded-3
                        d-flex flex-column align-items-center justify-content-center
                        text-center p-5"
                 style="min-height: 280px; border-style: dashed !important;">

                <i class="bi bi-plus-circle text-secondary mb-3"
                   style="font-size: 42px;"></i>

                <h6 class="fw-semibold mb-2">
                    Drag &amp; Drop file CSV di sini
                </h6>

                <p class="text-muted small mb-3">
                    Atau pilih file CSV dari perangkat kamu.
                </p>

                <input type="file"
                    id="fileCsv"
                    accept=".csv"
                    style="display: none;">

                <button type="button" 
                    class="btn btn-outline-primary px-4"
                    onclick="document.getElementById('fileCsv').click()">
                    <i class="bi bi-folder2-open me-2"></i>
                    Pilih File
                </button>

                <p id="namaFile" class="text-muted small mt-3 mb-0">
                    Belum ada file dipilih.
                </p>

            </div>

            <div id="previewCsv" class="mt-4" style="display: none;">

                <h6 class="fw-bold mb-2">Pratinjau Data CSV</h6>

                <div id="statusCsv" class="alert alert-info"></div>

                {{-- Tabel untuk menampilkan isi CSV --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Stok Minimum</th>
                            </tr>
                        </thead>

                        {{-- Isi tabel akan dibuat melalui JavaScript --}}
                        <tbody id="isiCsv">
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a href="{{ asset('templates/template_bahan.csv') }}"
                download="template_bahan.csv"
                class="btn btn-outline-secondary">

                    <i class="bi bi-download me-2"></i>
                    Download Template

                </a>

                <button type="button"
                        id="importDataBtn"
                        class="btn btn-primary"
                        disabled>

                    <i class="bi bi-upload me-2"></i>
                    Import Data

                </button>

            </div>

            <script>
                
                // Mengambil elemen input file dan tempat nama file.
                const inputFile = document.getElementById('fileCsv');
                const namaFile = document.getElementById('namaFile');

                // Mengambil elemen tombol dan area pratinjau.
                const tombolImport = document.getElementById('importDataBtn');
                const previewCsv = document.getElementById('previewCsv');
                const statusCsv = document.getElementById('statusCsv');
                const isiCsv = document.getElementById('isiCsv');

                // Menjalankan kode ketika pengguna memilih file.
                inputFile.addEventListener('change', function () {

                    // Memeriksa apakah ada file yang dipilih.
                    if (this.files.length > 0) {
                        namaFile.textContent = this.files[0].name;

                        // Mengaktifkan tombol Import Data.
                        tombolImport.disabled = false;
                    } else {
                        namaFile.textContent = 'Belum ada file dipilih.';

                        // Menonaktifkan tombol jika file tidak dipilih.
                        tombolImport.disabled = true;
                    }

                    // Menyembunyikan pratinjau sebelumnya.
                    previewCsv.style.display = 'none';
                });

                // Menjalankan kode ketika tombol Import Data diklik.
                tombolImport.addEventListener('click', async function () {

                    // Mengambil file yang dipilih.
                    const file = inputFile.files[0];

                    // Memastikan file sudah dipilih.
                    if (!file) {
                        alert('Silakan pilih file CSV terlebih dahulu.');
                        return;
                    }

                    // Memastikan ekstensi file adalah CSV.
                    if (!file.name.toLowerCase().endsWith('.csv')) {
                        alert('File harus berformat CSV.');
                        return;
                    }

                    try {
                        // Membaca isi file CSV.
                        const isi = await file.text();

                        // Mengambil judul kolom pada baris pertama.
                        const header = isi.split(/\r?\n/)[0].trim();

                        // Format kolom template bahan sementara.
                        const headerBenar =
                            'nama_bahan,kategori,satuan_dasar,stok,stok_minimum';

                        // Menampilkan area pratinjau.
                        previewCsv.style.display = 'block';

                        // Memeriksa apakah judul kolom sesuai.
                        if (header !== headerBenar) {
                            statusCsv.textContent =
                                'Format kolom CSV belum sesuai template bahan.';

                            statusCsv.className = 'alert alert-danger';
                            isiCsv.textContent = isi;
                            return;
                        }

                        // Mengosongkan isi tabel sebelumnya.
                        isiCsv.innerHTML = '';

                        // Mengambil semua baris CSV, kecuali judul kolom.
                        const baris = isi.trim().split(/\r?\n/);

                        // Mengambil data mulai baris kedua dan mengabaikan baris kosong.
                        const dataCsv = baris.slice(1).filter(data => data.trim() !== '');

                        // Menyiapkan daftar nomor baris yang memiliki kesalahan.
                        const barisError = [];

                        // Memeriksa setiap baris data CSV.
                        dataCsv.forEach((data, index) => {

                            // Memisahkan setiap kolom berdasarkan tanda koma.
                            const kolom = data.split(',');

                            const dataBersih = kolom.map(nilai => nilai.trim());

                            // Mengambil masing-masing nilai dari CSV.
                            const namaBahan = dataBersih[0] ?? '';
                            const kategori = dataBersih[1] ?? '';
                            const satuan = dataBersih[2] ?? '';
                            const stok = dataBersih[3] ?? '';
                            const stokMinimum = dataBersih[4] ?? '';

                            // Memastikan stok dan stok minimum berupa angka yg tidak negatif.
                            const stokValid = stok !== '' &&
                                Number.isFinite(Number(stok)) && Number(stok) >= 0;

                            const stokMinimumValid = stokMinimum !== '' &&
                                Number.isFinite(Number(stokMinimum)) && Number(stokMinimum) >= 0;

                            // Memeriksa apakah seluruh isi baris sdh sesuai aturan.
                            const valid =
                                dataBersih.length === 5 &&
                                namaBahan !== '' &&
                                kategori !== '' &&
                                ['g', 'ml', 'pcs'].includes(satuan) &&
                                stokValid &&
                                stokMinimumValid;

                            const tr = document.createElement('tr');

                            // MEmberi warna merah jika baris memiliki kesalahan.
                            if (!valid) {
                                tr.classList.add('table-danger');

                                // Menyimpan nomor baris CSV yg bermasalah.
                                barisError.push(index + 2);
                            }

                            // Menyiapkan nomr urut dan isi kolom tabel.
                            const nilaiBaris = [
                                index + 1,
                                namaBahan,
                                kategori,
                                satuan,
                                stok,
                                stokMinimum
                            ];

                            // Memasukkan setiap nilai ke dalam kolom tabel.
                            nilaiBaris.forEach(nilai => {

                                const td = document.createElement('td');

                                td.textContent = nilai;

                                tr.appendChild(td);
                            });
                            isiCsv.appendChild(tr);
                        });

                        // Menampilkan hasil validasi kepada pengguna.
                        if (barisError.length > 0) {

                            // Memberi peringatan jika ada baris y]g tidak sesuai.
                            statusCsv.textContent =
                                'Ada kesalahan pada baris CSV: ' + barisError.join(', ') +
                                '. Periksa baris yang berwarna merah.';

                            statusCsv.className = 'alert alert-warning';

                        } else {
                            statusCsv.textContent =
                                'Semua data CSV berhasil diperiksa dan formatnya sesuai.';

                            statusCsv.className = 'alert alert-success';
                        }

                    } catch (error) {
                        alert('File CSV gagal dibaca.');
                    }
                });

            </script>

        </div>
    </div>

</div>

@endsection
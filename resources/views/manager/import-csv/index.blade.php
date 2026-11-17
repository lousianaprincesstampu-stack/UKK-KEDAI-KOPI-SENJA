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
                    name="file_csv"
                    accept=".csv"
                    style="display: none;">

                <meta name="csrf-token" content="{{ csrf_token() }}">

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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Stok Awal</th>
                                <th>Satuan</th>
                                <th>Harga per satuan</th>
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
                
                const inputFile = document.getElementById('fileCsv');
                const namaFile = document.getElementById('namaFile');
                const tombolImport = document.getElementById('importDataBtn');
                const previewCsv = document.getElementById('previewCsv');
                const statusCsv = document.getElementById('statusCsv');
                const isiCsv = document.getElementById('isiCsv');


                inputFile.addEventListener('change', async function () {

                    const file = this.files[0];
.
                    if (!file) {
                        namaFile.textContent = 'Belum ada file dipilih.';
                        tombolImport.disabled = true;
                        previewCsv.style.display = 'none';
                        return;
                    }

                    namaFile.textContent = file.name;
.
                    if (!file.name.toLowerCase().endsWith('.csv')) {
                        alert('File harus berformat CSV.');
                        tombolImport.disabled = true;
                        return;
                    }

                    try {
                        const isi = await file.text();

                        const baris = isi.replace(/^\uFEFF/, '').split(/\r?\n/);

                        const headerBenar = [
                            'kode',
                            'nama_bahan',
                            'stok_awal',
                            'satuan',
                            'harga_per_satuan'
                        ];

                        const header = baris[0].split(',').map(nilai => nilai.trim());

                        previewCsv.style.display = 'block';

                        if (JSON.stringify(header) !== JSON.stringify(headerBenar)) {
                            statusCsv.textContent = 'Format CSV tidak sesuai dengan file bahan.csv sekolah.';
                            statusCsv.className = 'alert alert-danger';
                            isiCsv.innerHTML = '';
                            tombolImport.disabled = true;
                            return;
                        }

                        isiCsv.innerHTML = '';

                        const dataCsv = baris.slice(1).filter(data => data.trim() !== '');

                        const barisError = [];

                        dataCsv.forEach((data, index) => {

                            const kolom = data.split(',').map(nilai => nilai.trim());

                            const kode = kolom[0] ?? '';
                            const nama = kolom[1] ?? '';
                            const stok = kolom[2] ?? '';
                            const satuan = (kolom[3] ?? '').toLowerCase();
                            const harga = kolom[4] ?? '';

                            const stokValid = stok !== '' &&
                                Number.isFinite(Number(stok)) && Number(stok) >= 0;

                            const hargaValid = harga !== '' &&
                                Number.isFinite(Number(harga)) && Number(harga) >= 0;

                            const satuanValid = [
                                'kg', 'g', 'liter', 'ml', 'pcs'
                            ].includes(satuan);

                            const valid =
                                kolom.length === 5 &&
                                kode !== '' &&
                                nama !== '' &&
                                stokValid &&
                                hargaValid &&
                                satuanValid;

                            const tr = document.createElement('tr');

                            if (!valid) {
                                tr.classList.add('table-danger');
                                barisError.push(index + 2);
                            }

                            const nilaiBaris = [
                                index + 1,
                                kode,
                                nama,
                                stok,
                                satuan,
                                harga
                            ];

                            nilaiBaris.forEach(nilai => {
                                const td = document.createElement('td');
                                td.textContent = nilai;
                                tr.appendChild(td);
                            });
.
                            isiCsv.appendChild(tr);
                        });

                        if (barisError.length > 0 || dataCsv.length === 0) {
                            statusCsv.textContent = barisError.length > 0
                                ? 'Ada kesalahan pada baris: ' + barisError.join(', ')
                                : 'File CSV belum memiliki data bahan.';

                            statusCsv.className = 'alert alert-warning';
                            tombolImport.disabled = true;
                        } else {
                            statusCsv.textContent =
                                'Semua data CSV berhasil diperiksa dan formatnya sesuai.';

                            statusCsv.className = 'alert alert-success';

                            tombolImport.disabled = false;
                        }

                    } catch (error) {
                        alert('File CSV gagal dibaca.');
                        tombolImport.disabled = true;
                    }
                });

                tombolImport.addEventListener('click', async function () {

                    const file = inputFile.files[0];

                    if (!file) {
                        alert('Silakan pilih file CSV terlebih dahulu.');
                        return;
                    }

                    const formData = new FormData();

                    formData.append('file_csv', file);

                    try {
                        const response = await fetch("{{ route('manager.import-csv.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },

                            body: formData
                        });

                        const hasil = await response.json();

                        if (response.ok) {
                            alert(hasil.message ?? 'Import berhasil.');

                            tombolImport.disabled = true;
                        } else {
                            alert(hasil.message ?? 'Import gagal.');
                        }

                        } catch (error) {
                            alert('Terjadi kesalahan saat mengirim file CSV.');
                        }
                    });


            </script>

        </div>
    </div>

</div>

@endsection
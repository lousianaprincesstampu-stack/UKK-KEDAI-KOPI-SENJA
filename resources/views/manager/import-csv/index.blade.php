@extends('manager.layouts.app')
@section('title', 'Import CSV - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">Import CSV</h4>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Pilih ketiga file CSV yang diperlukan. Pastikan format dan isi file sudah sesuai sebelum melakukan import.
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row g-4">

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-box-seam text-primary fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">1. Data Bahan Baku</h5>
                            <p class="text-muted small mb-0">
                                Pilih file bahan.csv
                            </p>
                        </div>
                    </div>

                    <input type="file"
                           id="fileBahan"
                           accept=".csv"
                           class="form-control">

                    <p id="namaBahan" class="text-muted small mt-2 mb-0">
                        Belum ada file dipilih.
                    </p>

                    <div id="previewBahan" class="mt-3" style="display: none;">
                        <div id="statusBahan" class="alert"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Bahan</th>
                                        <th>Nama Bahan</th>
                                        <th>Stok Awal</th>
                                        <th>Satuan</th>
                                        <th>Harga per Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="isiBahan"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-cup-hot text-success fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">2. Data Menu</h5>
                            <p class="text-muted small mb-0">
                                Pilih file menu.csv
                            </p>
                        </div>
                    </div>

                    <input type="file"
                           id="fileMenu"
                           accept=".csv"
                           class="form-control">

                    <p id="namaMenu" class="text-muted small mt-2 mb-0">
                        Belum ada file dipilih.
                    </p>

                    <div id="previewMenu" class="mt-3" style="display: none;">
                        <div id="statusMenu" class="alert"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Menu</th>
                                        <th>Nama Menu</th>
                                        <th>Kategori</th>
                                        <th>Harga Jual</th>
                                    </tr>
                                </thead>
                                <tbody id="isiMenu"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-journal-text text-warning fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">3. Data Resep</h5>
                            <p class="text-muted small mb-0">
                                Pilih file resep.csv
                            </p>
                        </div>
                    </div>

                    <input type="file"
                           id="fileResep"
                           accept=".csv"
                           class="form-control">

                    <p id="namaResep" class="text-muted small mt-2 mb-0">
                        Belum ada file dipilih.
                    </p>

                    <div id="previewResep" class="mt-3" style="display: none;">
                        <div id="statusResep" class="alert"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Menu</th>
                                        <th>Nama Bahan</th>
                                        <th>Takaran</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="isiResep"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div id="hasilImport" class="mt-4" style="display: none;"></div>

    <div class="d-flex justify-content-end mt-4 mb-4">
        <button type="button"
                id="importDataBtn"
                class="btn btn-primary px-4"
                disabled>
            <i class="bi bi-upload me-2"></i>
            Import Semua Data
        </button>
    </div>

</div>

<script>
    const konfigurasi = [
        {
            jenis: 'Bahan',
            input: 'fileBahan',
            nama: 'namaBahan',
            preview: 'previewBahan',
            status: 'statusBahan',
            isi: 'isiBahan',
            header: [
                'kode',
                'nama_bahan',
                'stok_awal',
                'satuan',
                'harga_per_satuan'
            ],
            kolom: [
                'No',
                'Kode Bahan',
                'Nama Bahan',
                'Stok Awal',
                'Satuan',
                'Harga per Satuan'
            ],
            validasi: function (kolom) {
                const satuan = (kolom[3] ?? '').toLowerCase();

                return kolom.length === 5 &&
                    kolom[0] !== '' &&
                    kolom[1] !== '' &&
                    kolom[2] !== '' &&
                    Number.isFinite(Number(kolom[2])) &&
                    Number(kolom[2]) >= 0 &&
                    ['kg', 'g', 'liter', 'ml', 'pcs'].includes(satuan) &&
                    kolom[4] !== '' &&
                    Number.isFinite(Number(kolom[4])) &&
                    Number(kolom[4]) >= 0;
            }
        },
        {
            jenis: 'Menu',
            input: 'fileMenu',
            nama: 'namaMenu',
            preview: 'previewMenu',
            status: 'statusMenu',
            isi: 'isiMenu',
            header: [
                'kode',
                'nama_menu',
                'kategori',
                'harga_jual'
            ],
            kolom: [
                'No',
                'Kode Menu',
                'Nama Menu',
                'Kategori',
                'Harga Jual'
            ],
            validasi: function (kolom) {
                return kolom.length === 4 &&
                    kolom[0] !== '' &&
                    kolom[1] !== '' &&
                    ['Minuman', 'Makanan'].includes(kolom[2]) &&
                    kolom[3] !== '' &&
                    Number.isFinite(Number(kolom[3])) &&
                    Number(kolom[3]) >= 0;
            }
        },
        {
            jenis: 'Resep',
            input: 'fileResep',
            nama: 'namaResep',
            preview: 'previewResep',
            status: 'statusResep',
            isi: 'isiResep',
            header: [
                'kode_menu',
                'nama_bahan',
                'takaran',
                'satuan'
            ],
            kolom: [
                'No',
                'Kode Menu',
                'Nama Bahan',
                'Takaran',
                'Satuan'
            ],
            validasi: function (kolom) {
                const satuan = (kolom[3] ?? '').toLowerCase();

                return kolom.length === 4 &&
                    kolom[0] !== '' &&
                    kolom[1] !== '' &&
                    kolom[2] !== '' &&
                    Number.isFinite(Number(kolom[2])) &&
                    Number(kolom[2]) > 0 &&
                    ['kg', 'g', 'liter', 'ml', 'pcs'].includes(satuan);
            }
        }
    ];

    const statusFile = {};
    const tombolImport = document.getElementById('importDataBtn');

    function pecahBaris(baris) {
        const hasil = [];
        let nilai = '';
        let dalamKutip = false;

        for (let i = 0; i < baris.length; i++) {
            const karakter = baris[i];

            if (karakter === '"') {
                if (dalamKutip && baris[i + 1] === '"') {
                    nilai += '"';
                    i++;
                } else {
                    dalamKutip = !dalamKutip;
                }
            } else if (karakter === ',' && !dalamKutip) {
                hasil.push(nilai.trim());
                nilai = '';
            } else {
                nilai += karakter;
            }
        }

        hasil.push(nilai.trim());

        return hasil;
    }

    function perbaruiTombol() {
        tombolImport.disabled = !konfigurasi.every(
            item => statusFile[item.jenis] === true
        );
    }

    function buatSel(baris, nilai) {
        const td = document.createElement('td');
        td.textContent = nilai;
        baris.appendChild(td);
    }

    async function periksaFile(item) {
        const input = document.getElementById(item.input);
        const namaFile = document.getElementById(item.nama);
        const preview = document.getElementById(item.preview);
        const status = document.getElementById(item.status);
        const isi = document.getElementById(item.isi);

        statusFile[item.jenis] = false;
        perbaruiTombol();

        isi.innerHTML = '';
        preview.style.display = 'none';

        const file = input.files[0];

        if (!file) {
            namaFile.textContent = 'Belum ada file dipilih.';
            return;
        }

        namaFile.textContent = file.name;

        if (!file.name.toLowerCase().endsWith('.csv')) {
            status.textContent = 'File harus berformat CSV.';
            status.className = 'alert alert-danger';
            preview.style.display = 'block';
            return;
        }

        try {
            const teks = (await file.text()).replace(/^\uFEFF/, '');
            const baris = teks.split(/\r?\n/);
            const header = pecahBaris(baris[0] ?? '');

            preview.style.display = 'block';

            if (JSON.stringify(header) !== JSON.stringify(item.header)) {
                status.textContent =
                    'Header CSV tidak sesuai. Periksa kembali susunan kolom file.';
                status.className = 'alert alert-danger';
                return;
            }

            const data = baris.slice(1).filter(
                barisData => barisData.trim() !== ''
            );

            if (data.length === 0) {
                status.textContent = 'File CSV belum memiliki data.';
                status.className = 'alert alert-warning';
                return;
            }

            const barisError = [];

            data.forEach((barisData, index) => {
                const kolom = pecahBaris(barisData);
                const valid = item.validasi(kolom);
                const tr = document.createElement('tr');

                if (!valid) {
                    tr.classList.add('table-danger');
                    barisError.push(index + 2);
                }

                buatSel(tr, index + 1);

                kolom.forEach(nilai => {
                    buatSel(tr, nilai);
                });

                isi.appendChild(tr);
            });

            if (barisError.length > 0) {
                status.textContent =
                    'Ada data yang perlu diperiksa pada baris: ' +
                    barisError.join(', ');

                status.className = 'alert alert-warning';
                return;
            }

            status.textContent =
                data.length + ' baris data berhasil diperiksa. Format file sesuai.';

            status.className = 'alert alert-success';
            statusFile[item.jenis] = true;

        } catch (error) {
            status.textContent = 'File CSV gagal dibaca.';
            status.className = 'alert alert-danger';
            preview.style.display = 'block';
        }

        perbaruiTombol();
    }

    konfigurasi.forEach(item => {
        document.getElementById(item.input).addEventListener(
            'change',
            () => periksaFile(item)
        );
    });

    tombolImport.addEventListener('click', async function () {
        const formData = new FormData();

        konfigurasi.forEach(item => {
            const file = document.getElementById(item.input).files[0];
            const namaField = 'file_' + item.jenis.toLowerCase();

            formData.append(namaField, file);
        });

        tombolImport.disabled = true;
        tombolImport.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        try {
            const response = await fetch(
                "{{ route('manager.import-csv.store') }}",
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        'Accept': 'application/json'
                    },
                    body: formData
                }
            );

            const hasil = await response.json();

            if (!response.ok) {
                throw new Error(
                    hasil.message ?? 'Import gagal dilakukan.'
                );
            }

            tampilkanHasil(hasil);

            konfigurasi.forEach(item => {
                document.getElementById(item.input).value = '';
                document.getElementById(item.nama).textContent =
                    'Belum ada file dipilih.';
                document.getElementById(item.preview).style.display = 'none';
                document.getElementById(item.isi).innerHTML = '';
                statusFile[item.jenis] = false;
            });

        } catch (error) {
            alert(error.message ?? 'Terjadi kesalahan saat import.');
        } finally {
            tombolImport.innerHTML =
                '<i class="bi bi-upload me-2"></i>Import Semua Data';

            perbaruiTombol();
        }
    });

    function tampilkanHasil(hasil) {
        const tempat = document.getElementById('hasilImport');

        tempat.innerHTML = '';
        tempat.style.display = 'block';

        const judul = document.createElement('h5');
        judul.className = 'fw-bold mb-3';
        judul.textContent = hasil.message ?? 'Hasil Import';
        tempat.appendChild(judul);

        [
            ['Bahan Baku', hasil.bahan],
            ['Menu', hasil.menu],
            ['Resep', hasil.resep]
        ].forEach(([nama, data]) => {
            if (!data) return;

            const card = document.createElement('div');
            card.className = 'card border-0 shadow-sm mb-3';

            const body = document.createElement('div');
            body.className = 'card-body';

            const heading = document.createElement('h6');
            heading.className = 'fw-bold';
            heading.textContent = nama;

            const ringkasan = document.createElement('p');
            ringkasan.className = 'mb-2';
            ringkasan.textContent =
                'Berhasil: ' + data.berhasil +
                ' | Dilewati: ' + data.dilewati;

            body.appendChild(heading);
            body.appendChild(ringkasan);

            const daftar = document.createElement('ul');
            daftar.className = 'small text-muted mb-0';

            (data.details ?? []).forEach(detail => {
                const item = document.createElement('li');
                item.textContent = detail;
                daftar.appendChild(item);
            });

            body.appendChild(daftar);
            card.appendChild(body);
            tempat.appendChild(card);
        });

        tempat.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
</script>

@endsection

@extends('cashier.layouts.app')
@section('title', 'Pesanan Offline - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-4">Pesanan Offline</h3>

    <div class="row g-4">

        <div class="col-lg-7">

            <div class="card dashboard-card">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Daftar Menu</h5>

                    <input
                        type="text"
                        id="cariMenu"
                        class="form-control mb-3"
                        placeholder="Cari nama menu..."
                    >

                    <div class="row g-3" id="daftarMenu">

                        @forelse ($menus as $menu)

                            <div class="col-md-6 item-menu"
                                data-nama="{{ strtolower($menu->nama_menu) }}">

                                <div class="border rounded p-3 h-100">

                                    <h6 class="fw-bold">
                                        {{ $menu->nama_menu }}
                                    </h6>

                                    <p class="text-muted mb-3">
                                        Rp{{ number_format($menu->harga_jual, 0, ',', '.') }}
                                    </p>

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm w-100 tombol-tambah"
                                        data-id="{{ $menu->id }}"
                                        data-nama="{{ $menu->nama_menu }}"
                                        data-harga="{{ $menu->harga_jual }}"
                                    >
                                        Tambah
                                    </button>

                                </div>
                            </div>

                        @empty

                            <div class="col-12">
                                <p class="text-muted text-center py-4">
                                    Belum ada menu yang dapat dipesan.
                                </p>
                            </div>

                        @endforelse

                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-5">

        <div class="card-body">

            <h5 class="fw-bold mb-3">Rincian Pesanan</h5>

            <form action="{{ route('cashier.orders.store-offline') }}" method="POST" id="formPesanan">
                    @csrf

                    <div id="daftarPesanan">

                        <p class="text-muted text-center py-4" id="pesananKosong">
                            Belum ada menu yang dipilih.
                        </p>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong id="subtotal">Rp0</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Service (5%)</span>
                        <strong id="service">Rp0</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Pajak (10%)</span>
                        <strong id="pajak">Rp0</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong class="text-primary fs-5" id="total">
                            Rp0
                        </strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nomor Meja
                        </label>

                        <select id="pilihMeja" name="cafe_table_id" class="form-select" required>
                            <option value="">Pilih meja</option>

                            @foreach ($meja as $item)
                                <option value="{{ $item->id }}">
                                    Meja {{ $item->nomor_meja }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Metode Pembayaran
                        </label>

                        <select id="metodePembayaran" name="metode_pembayaran" class="form-select" required>
                            <option value="">Pilih pembayaran</option>
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                        </select>

                        <button type="submit" class="btn btn-primary w-100 mt-3" id="tombolSimpan">
                        Simpan Pesanan
                    </button>
                    
                    </div>

                    <p class="text-muted small mb-0">
                        Periksa kembali rincian pesanan sebelum menyimpannya.
                    </p>

                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let keranjang = [];

    function rupiah(angka) {
        return 'Rp' + Number(angka).toLocaleString('id-ID');
    }

    function tampilkanPesanan() {

        const daftar = document.getElementById('daftarPesanan');
        const kosong = document.getElementById('pesananKosong');

        daftar.innerHTML = '';

        let subtotal = 0;

        if (keranjang.length === 0) {
            daftar.innerHTML = `
                <p class="text-muted text-center py-4">
                    Belum ada menu yang dipilih.
                </p>
            `;
        }

        keranjang.forEach((item, index) => {

            const jumlahHarga = item.harga * item.jumlah;

            subtotal += jumlahHarga;

            const baris = document.createElement('div');
            baris.className = 'border-bottom py-3';

            const nama = document.createElement('strong');
            nama.textContent = item.nama;

            const harga = document.createElement('div');
            harga.className = 'text-muted small';
            harga.textContent = rupiah(item.harga) + ' / menu';

            const kontrol = document.createElement('div');
            kontrol.className = 'd-flex justify-content-between align-items-center mt-2';

            const tombol = document.createElement('div');
            tombol.className = 'd-flex align-items-center gap-2';

            const kurang = document.createElement('button');
            kurang.type = 'button';
            kurang.className = 'btn btn-sm btn-outline-secondary';
            kurang.textContent = '-';
            kurang.addEventListener('click', function () {
                ubahJumlah(index, -1);
            });

            const jumlah = document.createElement('span');
            jumlah.textContent = item.jumlah;

            const tambah = document.createElement('button');
            tambah.type = 'button';
            tambah.className = 'btn btn-sm btn-outline-secondary';
            tambah.textContent = '+';
            tambah.addEventListener('click', function () {
                ubahJumlah(index, 1);
            });

            tombol.append(kurang, jumlah, tambah);

            const subtotalMenu = document.createElement('strong');
            subtotalMenu.textContent = rupiah(jumlahHarga);

            kontrol.append(tombol, subtotalMenu);

            baris.append(nama, harga, kontrol);

            daftar.appendChild(baris);
        });

        const service = subtotal * 0.05;

        const pajak = (subtotal + service) * 0.10;

        const total = subtotal + service + pajak;

        document.getElementById('subtotal').textContent = rupiah(subtotal);
        document.getElementById('service').textContent = rupiah(service);
        document.getElementById('pajak').textContent = rupiah(pajak);
        document.getElementById('total').textContent = rupiah(total);
    }

    function ubahJumlah(index, perubahan) {

        keranjang[index].jumlah += perubahan;

        if (keranjang[index].jumlah <= 0) {
            keranjang.splice(index, 1);
        }

        tampilkanPesanan();
    }

    document.querySelectorAll('.tombol-tambah').forEach(function (tombol) {

        tombol.addEventListener('click', function () {

            const id = Number(this.dataset.id);

            const menu = keranjang.find(item => item.id === id);

            if (menu) {
                menu.jumlah++;
            } else {
                keranjang.push({
                    id: id,
                    nama: this.dataset.nama,
                    harga: Number(this.dataset.harga),
                    jumlah: 1
                });
            }

            tampilkanPesanan();
        });

    });

        document.getElementById('cariMenu').addEventListener('input', function () {

        const kataKunci = this.value.toLowerCase();

        document.querySelectorAll('.item-menu').forEach(function (item) {

            const nama = item.dataset.nama;

            item.style.display = nama.includes(kataKunci) ? '' : 'none';

        });

    });

    document.getElementById('formPesanan').addEventListener('submit', function (event) {

        if (keranjang.length === 0) {
            event.preventDefault();
            alert('Pilih minimal satu menu.');
            return;
        }

        keranjang.forEach(function (item, index) {

            const inputMenu = document.createElement('input');
            inputMenu.type = 'hidden';
            inputMenu.name = `items[${index}][menu_id]`;
            inputMenu.value = item.id;

            const inputJumlah = document.createElement('input');
            inputJumlah.type = 'hidden';
            inputJumlah.name = `items[${index}][jumlah]`;
            inputJumlah.value = item.jumlah;

            this.appendChild(inputMenu);
            this.appendChild(inputJumlah);

        }, this);

    });

});

</script>

@endsection

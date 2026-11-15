@extends('manager.layouts.app')
@section('content')

<div class="container-fluid">

    {{-- Judul halaman --}}
    <div class="mb-4">
        <h3>Tambah Resep Menu</h3>
        <p class="text-muted">
            Tentukan bahan dan jumlah yang digunakan untuk satu porsi menu.
        </p>
    </div>

    {{-- Menampilkan pesan kesalahan umum --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form tambah resep --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('manager.recipes.store') }}" method="POST">
                @csrf

                {{-- Pilihan menu --}}
                <div class="mb-4">
                    <label for="menu_id" class="form-label">
                        Pilih Menu
                    </label>

                    <select name="menu_id"
                            id="menu_id"
                            class="form-select @error('menu_id') is-invalid @enderror">

                        <option value="">-- Pilih Menu --</option>

                        @foreach ($menus as $menu)
                            <option value="{{ $menu->id }}"
                                {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                {{ $menu->nama_menu }}
                            </option>
                        @endforeach

                    </select>

                    @error('menu_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- Informasi jika semua menu sudah memiliki resep --}}
                    @if ($menus->isEmpty())
                        <small class="text-danger">
                            Semua menu sudah memiliki resep atau belum ada menu.
                        </small>
                    @endif
                </div>

                {{-- Bagian komposisi bahan --}}
                <h5 class="mb-3">Komposisi Bahan per Porsi</h5>

                <div id="daftar-bahan">

                    {{-- Baris bahan pertama --}}
                    <div class="row g-2 mb-3 baris-bahan">

                        <div class="col-md-6">
                            <label class="form-label">Nama Bahan</label>

                            <select name="bahan_id[]"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Bahan --</option>

                                @foreach ($bahans as $bahan)
                                    <option value="{{ $bahan->id }}">
                                        {{ $bahan->nama_bahan }}
                                        ({{ $bahan->satuan_dasar }})
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah per Porsi</label>

                            <input type="number"
                                   name="jumlah[]"
                                   class="form-control"
                                   placeholder="Contoh: 18"
                                   min="0.001"
                                   step="0.001"
                                   required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button"
                                    class="btn btn-danger w-100"
                                    onclick="hapusBahan(this)"
                                    disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                    </div>

                </div>

                <button type="button"
                        class="btn btn-outline-primary mb-4"
                        onclick="tambahBahan()">
                    <i class="bi bi-plus-circle"></i> Tambah Bahan
                </button>

                <div class="d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary"
                            {{ $menus->isEmpty() || $bahans->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-save"></i> Simpan Resep
                    </button>

                    <a href="{{ route('manager.recipes.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- JavaScript utk menambahkan dan menghapus baris bahan --}}
<script>
    // Menghitung jumlah baris bahan yang tersedia.
    function perbaruiTombolHapus() {
        const semuaBaris = document.querySelectorAll('.baris-bahan');

        // Tombol hapus aktif jika terdapat lebih dari satu baris.
        semuaBaris.forEach(function (baris) {
            const tombol = baris.querySelector('button');

            tombol.disabled = semuaBaris.length === 1;
        });
    }

    // Menambahkan baris bahan baru.
    function tambahBahan() {
        const daftar = document.getElementById('daftar-bahan');
        const barisPertama = daftar.querySelector('.baris-bahan');

        // Menggandakan baris pertama sebagai baris baru.
        const barisBaru = barisPertama.cloneNode(true);

        // Mengosongkan pilihan bahan dan jumlah.
        barisBaru.querySelector('select').value = '';
        barisBaru.querySelector('input').value = '';

        // Menambahkan baris baru ke formulir.
        daftar.appendChild(barisBaru);

        perbaruiTombolHapus();
    }

    // Menghapus baris bahan yang dipilih.
    function hapusBahan(tombol) {
        const daftar = document.getElementById('daftar-bahan');
        const semuaBaris = daftar.querySelectorAll('.baris-bahan');

        // Baris hanya bisa dihapus jika masih ada baris lainnya.
        if (semuaBaris.length > 1) {
            tombol.closest('.baris-bahan').remove();
        }

        perbaruiTombolHapus();
    }

    perbaruiTombolHapus();
</script>

@endsection

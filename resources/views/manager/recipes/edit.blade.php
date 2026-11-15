@extends('manager.layouts.app')
@section('content')

<div class="mb-4">
    <h3 class="fw-bold">Edit Resep Menu</h3>
    <p class="text-muted">Ubah komposisi bahan dan jumlah yang digunakan.</p>
</div>

{{-- Menampilkan pesan kesalahan validasi --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form untuk memperbarui resep --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        {{-- Nama menu yang resepnya sedang diedit --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Nama Menu</label>
            <input type="text"
                   class="form-control"
                   value="{{ $recipe->menu->nama_menu }}"
                   disabled>
        </div>

        <form action="{{ route('manager.recipes.update', $recipe->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <label class="form-label fw-semibold">Komposisi Bahan</label>

            {{-- Menampilkan bahan yang sudah tersimpan --}}
            <div id="daftar-bahan">

                @foreach ($recipe->recipeDetails as $detail)
                    <div class="row g-2 mb-3 baris-bahan">

                        <div class="col-md-6">
                            <select name="bahan_id[]" class="form-select" required>
                                @foreach ($bahans as $bahan)
                                    <option value="{{ $bahan->id }}"
                                        {{ $detail->bahan_id == $bahan->id ? 'selected' : '' }}>
                                        {{ $bahan->nama_bahan }} ({{ $bahan->satuan_dasar }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="number"
                                   name="jumlah[]"
                                   class="form-control"
                                   step="0.001"
                                   min="0.001"
                                   value="{{ rtrim(rtrim(number_format((float) $detail->jumlah, 3, '.', ''), '0'), '.') }}"
                                   required>
                        </div>

                        <div class="col-md-2">
                            <button type="button"
                                    class="btn btn-outline-danger w-100"
                                    onclick="hapusBahan(this)">
                                Hapus
                            </button>
                        </div>

                    </div>
                @endforeach

            </div>

            <button type="button"
                    class="btn btn-outline-primary mb-4"
                    onclick="tambahBahan()">
                + Tambah Bahan
            </button>

            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('manager.recipes.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

{{-- JavaScript untuk menambah dan menghapus baris bahan --}}
<script>
    // Menambahkan pilihan bahan baru.
    function tambahBahan() {
        const daftar = document.getElementById('daftar-bahan');
        const baris = daftar.querySelector('.baris-bahan').cloneNode(true);

        // Mengosongkan pilihan dan jumlah pada baris baru.
        baris.querySelector('select').selectedIndex = 0;
        baris.querySelector('input').value = '';

        daftar.appendChild(baris);
    }

    // Menghapus baris bahan jika masih ada baris lainnya.
    function hapusBahan(tombol) {
        const daftar = document.getElementById('daftar-bahan');

        if (daftar.querySelectorAll('.baris-bahan').length > 1) {
            tombol.closest('.baris-bahan').remove();
        } else {
            alert('Resep harus memiliki minimal satu bahan.');
        }
    }
</script>

@endsection
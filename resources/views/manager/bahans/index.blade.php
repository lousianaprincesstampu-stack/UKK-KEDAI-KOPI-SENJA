@extends('manager.layouts.app')
@section('title', 'Data Bahan - Kedai Kopi Senja')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold">Data Bahan</h3>

        <p class="text-muted">
            Kelola data bahan dan pantau persediaan Kedai Kopi Senja.
        </p>
    </div>

    <div class="table-container">

        <div class="d-flex justify-content-between align-items-center mb-3">

    {{-- Form pencarian bahan --}}
    <form action="{{ route('manager.bahans.index') }}"
          method="GET"
          class="d-flex gap-2">

        {{-- Input nama bahan yang ingin dicari --}}
        <input type="text"
               name="cari"
               class="form-control"
               placeholder="Cari nama bahan..."
               value="{{ $cari ?? '' }}"
               style="width: 250px;">

        {{-- Tombol pencarian --}}
        <button type="submit" class="btn btn-outline-secondary">
            <i class="bi bi-search"></i> Cari
        </button>
    </form>

    {{-- Tombol tambah bahan (sementara belum dihubungkan) --}}
    <a href="{{ route('manager.bahans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Bahan
    </a>

</div>
        {{-- Tabel data bahan --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">

                {{-- Kepala tabel --}}
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Stok Minimum</th>
                        <th>Harga per Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                {{-- Isi tabel --}}
                <tbody>
                    @forelse ($bahans as $bahan)
                        <tr>
                            {{-- Nomor urut --}}
                            <td>{{ $loop->iteration }}</td>

                            {{-- Nama bahan --}}
                            <td>{{ $bahan->nama_bahan }}</td>

                            {{-- Satuan bahan --}}
                            <td>{{ $bahan->satuan_dasar }}</td>

                            {{-- Stok tersedia --}}
                            <td>
                                {{ rtrim(rtrim(number_format((float) $bahan->stok, 3, ',', '.'), '0'), ',') }}
                            </td>

                            {{-- Stok minimum --}}
                            <td>
                                {{ rtrim(rtrim(number_format((float) $bahan->stok_minimum, 3, ',', '.'), '0'), ',') }}
                            </td>

                            {{-- Harga per satuan --}}
                            <td>
                                Rp {{ number_format((float) $bahan->harga_per_satuan, 0, ',', '.') }}
                            </td>

                            {{-- Form pengaturan stok minimum --}}
                            <td>
                                <form action="{{ route('manager.bahans.update-minimum-stock', $bahan->id) }}"
                                    method="POST"
                                    class="d-flex gap-2">

                                    @csrf
                                    @method('PATCH')

                                    <input type="number"
                                        name="stok_minimum"
                                        class="form-control form-control-sm"
                                        value="{{ old('stok_minimum', (float) $bahan->stok_minimum) }}"
                                        min="0"
                                        step="0.001"
                                        style="width: 110px;"
                                        required>

                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data bahan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

@endsection

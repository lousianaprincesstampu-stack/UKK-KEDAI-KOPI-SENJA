@extends('manager.layouts.app')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3>Resep Menu</h3>
        <p class="text-muted">
            Kelola komposisi bahan yang digunakan untuk setiap menu.
        </p>
    </div>

    {{-- Tabel daftar menu dan resep --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

           <div class="d-flex justify-content-between align-items-center mb-3">

                {{-- Form pencarian berdasarkan nama menu --}}
                <form action="{{ route('manager.recipes.index') }}"
                    method="GET"
                    class="d-flex gap-2">

                <input type="text"
                    name="cari"
                    class="form-control"
                    placeholder="Cari nama menu..."
                    value="{{ $cari ?? '' }}"
                    style="width: 250px;">

                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </form>

                <a href="{{ route('manager.recipes.create') }}" class="btn btn-primary">
                    <i clas= "bi bi-plus-circle"></i> Tambah Resep
                </a>

    </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    {{-- Kepala tabel --}}
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Komposisi Bahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    {{-- Isi tabel --}}
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- Nama menu --}}
                                <td>{{ $menu->nama_menu }}</td>

                                {{-- Daftar bahan resep --}}
                                <td>
                                    @if ($menu->recipe && $menu->recipe->recipeDetails->isNotEmpty())
                                        @foreach ($menu->recipe->recipeDetails as $detail)
                                            <div>
                                                {{ $detail->bahan->nama_bahan ?? 'Bahan tidak ditemukan' }}
                                                
                                                {{ rtrim(rtrim(number_format((float) $detail->jumlah, 3, ',', '.'), '0'), ',') }}
                                                {{ $detail->bahan->satuan_dasar ?? '' }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">
                                            Resep belum dibuat.
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($menu->recipe)

                                        {{-- Tombol untuk mengedit komposisi resep --}}
                                        <a href="{{ route('manager.recipes.edit', $menu->recipe->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i> Kelola Resep
                                        </a>

                                        {{-- Form untuk menghapus resep --}}
                                        <form action="{{ route('manager.recipes.destroy', $menu->recipe->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus resep menu ini?')">

                                            @csrf
                                            @method('DELETE')

                                            {{-- Tombol hapus resep --}}
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>

                                    @else

                                        {{-- Informasi jika resep belum tersedia --}}
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            Belum Ada Resep
                                        </button>

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada data menu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection

@extends('manager.layouts.app')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3>Resep Menu</h3>
        <p class="text-muted">
            Kelola komposisi bahan yang digunakan untuk setiap menu.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

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
                    <i class="bi bi-plus-circle"></i> Tambah Resep
                </a>

            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Status Resep</th>
                            <th>Komposisi Bahan</th>
                            <th>HPP</th>
                            <th>Margin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $menu->nama_menu }}</td>

                                <td>
                                    @if ($menu->status_stok === 'Tidak Tersedia')
                                        <span class="badge text-bg-warning">
                                            Resep Belum Lengkap
                                        </span>
                                    @elseif ($menu->recipe && $menu->recipe->recipeDetails->isNotEmpty())
                                        <span class="badge text-bg-success">
                                            Lengkap
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Belum Dibuat
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($menu->recipe && $menu->recipe->recipeDetails->isNotEmpty())

                                        @foreach ($menu->recipe->recipeDetails as $detail)
                                            <div>
                                                {{ $detail->bahan->nama_bahan ?? 'Bahan tidak ditemukan' }}

                                                {{ rtrim(rtrim(number_format((float) $detail->jumlah, 3, ',', '.'), '0'), ',') }}

                                                {{ $detail->bahan->satuan_dasar ?? '' }}
                                            </div>
                                        @endforeach

                                        @if ($menu->status_stok === 'Tidak Tersedia')
                                            <div class="text-danger small mt-2">
                                                <i class="bi bi-exclamation-circle"></i>
                                                Ada bahan resep yang belum terdaftar.
                                            </div>
                                        @endif

                                    @else
                                        <span class="text-muted">
                                            Resep belum dibuat.
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($menu->recipe)
                                        Rp{{ number_format($menu->hpp, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($menu->recipe)
                                        <span class="{{ $menu->margin < 0 ? 'text-danger' : 'text-success' }}">
                                            Rp{{ number_format($menu->margin, 2, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($menu->recipe)

                                        <a href="{{ route('manager.recipes.edit', $menu->recipe->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i> Kelola Resep
                                        </a>

                                        <form action="{{ route('manager.recipes.destroy', $menu->recipe->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus resep menu ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>

                                    @else

                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            Belum Ada Resep
                                        </button>

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
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

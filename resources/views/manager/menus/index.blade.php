
{{-- Menggunakan layout utama Manager --}}
@extends('manager.layouts.app')

{{-- Mengatur judul halaman --}}
@section('title', 'Data Menu - Kedai Kopi Senja')

{{-- Memulai isi halaman Data Menu --}}
@section('content')

    <!-- Judul Halaman -->
    <div class="mb-4">
        <h3 class="fw-bold">Data Menu</h3>

        <p class="text-muted">
            Kelola daftar menu Kedai Kopi Senja.
        </p>
    </div>

    <!-- Tabel Data Menu -->
    <div class="table-container">

        <!-- Judul tabel dan tombol tambah -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Menu</h5>

            <button type="button" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Menu
            </button>
        </div>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $menu->nama_menu }}</td>

                            <td>
                                Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}
                            </td>

                            <td>
                                @if ($menu->status)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Belum ada data menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

@endsection

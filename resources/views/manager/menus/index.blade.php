@extends('manager.layouts.app')
@section('title', 'Data Menu - Kedai Kopi Senja')
@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <div class="mb-4">
        <h3 class="fw-bold">Data Menu</h3>

        <p class="text-muted">
            Kelola daftar menu Kedai Kopi Senja.
        </p>
    </div>

    <div class="table-container">

        {{-- Bagian pencarian dan tombol tambah menu --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            {{-- Form pencarian menu --}}
            <form action="{{ route('manager.menus.index') }}"
                method="GET"
                class="d-flex gap-2">

                {{-- Input kata kunci pencarian --}}
                <input type="text"
                    name="cari"
                    class="form-control"
                    placeholder="Cari nama menu..."
                    value="{{ $cari ?? '' }}"
                    style="width: 250px;">

                {{-- Tombol pencarian --}}
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i> Cari
                </button>

            </form>

            {{-- Tombol tambah menu --}}
            <a href="{{ route('manager.menus.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Menu
            </a>

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
                        <th>Aksi</th>
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

                            <td>
                                <a href="{{ route('manager.menus.edit', $menu->id) }}"
                                    class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('manager.menus.toggle-status', $menu->id) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            class="btn btn-sm {{ $menu->status ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('Apakah kamu yakin ingin mengubah status menu ini?')">

                                        @if ($menu->status)
                                            Nonaktifkan
                                        @else
                                            Aktifkan
                                        @endif

                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

@endsection

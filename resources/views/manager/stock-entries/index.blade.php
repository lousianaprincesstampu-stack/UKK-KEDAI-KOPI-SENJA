
@extends('manager.layouts.app')
@section('title', 'Stok Masuk')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Stok Masuk</h4>
        <p class="text-muted mb-0">Riwayat penambahan stok bahan.</p>
    </div>

    <a href="{{ route('manager.stock-entries.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Stok Masuk
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('manager.stock-entries.index') }}"
      method="GET"
      class="d-flex gap-2 mb-3">

    <input type="text"
           name="cari"
           class="form-control"
           placeholder="Cari nama bahan..."
           value="{{ $cari ?? '' }}"
           style="max-width: 300px;">

    <button type="submit" class="btn btn-outline-secondary">
        <i class="bi bi-search"></i> Cari
    </button>

    @if (!empty($cari))
        <a href="{{ route('manager.stock-entries.index') }}"
           class="btn btn-outline-danger">
            Reset
        </a>
    @endif
</form>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Bahan</th>
                    <th>Jumlah Masuk</th>
                    <th>Keterangan</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($stockEntries as $entry)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $entry->tanggal ? $entry->tanggal->format('d-m-Y') : '-' }}
                        </td>

                        <td>
                            {{ $entry->bahan->nama_bahan ?? 'Bahan tidak ditemukan' }}
                        </td>

                        <td>
                            {{ rtrim(rtrim(number_format((float) $entry->jumlah, 3, ',', '.'), '0'), ',') }}
                            {{ $entry->bahan->satuan_dasar ?? '' }}
                        </td>

                        <td>{{ $entry->keterangan ?? '-' }}</td>

                        <td>{{ $entry->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            @if (!empty($cari))
                                Bahan yang dicari tidak ditemukan.
                            @else
                                Belum ada riwayat stok masuk.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

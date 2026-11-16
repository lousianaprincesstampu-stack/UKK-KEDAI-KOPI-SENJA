@extends('manager.layouts.app')
@section('title', 'Laporan Penjualan - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Laporan Penjualan</h3>
        <p class="text-muted">Laporan penjualan berdasarkan menu dan periode tanggal.</p>
    </div>

    {{-- Form untuk memilih periode laporan --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('manager.reports.sales') }}" method="GET">
                <div class="row align-items-end g-3">

                    {{-- Filter tanggal awal --}}
                    <div class="col-md-4">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input
                            type="date"
                            name="tanggal_awal"
                            id="tanggal_awal"
                            class="form-control"
                            value="{{ $tanggalAwal }}"
                            required
                        >
                    </div>

                    {{-- Filter tanggal akhir --}}
                    <div class="col-md-4">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input
                            type="date"
                            name="tanggal_akhir"
                            id="tanggal_akhir"
                            class="form-control"
                            value="{{ $tanggalAkhir }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Tampilkan Laporan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan laporan --}}
    <div class="row g-3 mb-4">

        {{-- Total menu terjual --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Menu Terjual</p>
                    <h4 class="fw-bold mb-0">
                        {{ number_format($totalMenuTerjual, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- Total penjualan --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Penjualan</p>
                    <h4 class="fw-bold text-success mb-0">
                        Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel rincian penjualan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Rincian Penjualan per Menu</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Penjualan</th>
                        </tr>
                    </thead>

                    {{-- Isi tabel --}}
                    <tbody>
                        @forelse ($penjualan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_menu }}</td>
                                <td>{{ number_format($item->total_terjual, 0, ',', '.') }}</td>
                                <td>
                                    Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            {{-- Ditampilkan jika belum ada transaksi --}}
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data penjualan pada periode ini.
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

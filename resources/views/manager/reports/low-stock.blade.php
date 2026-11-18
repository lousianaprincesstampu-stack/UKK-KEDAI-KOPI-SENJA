@extends('manager.layouts.app')
@section('title', 'Laporan Stok Menipis - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Laporan Stok Menipis</h3>
        <p class="text-muted">
            Daftar bahan yang stoknya sudah mencapai atau berada di bawah batas minimum.
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-1">Total Bahan Menipis</p>

                    <h4 class="fw-bold text-danger mb-0">
                        {{ number_format($totalBahanMenipis, 0, ',', '.') }}
                    </h4>

                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Rincian Stok Bahan</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan</th>
                            <th>Stok Saat Ini</th>
                            <th>Stok Minimum</th>
                            <th>Satuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bahanMenipis as $bahan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $bahan->nama_bahan }}</td>

                                <td>
                                    {{ number_format($bahan->stok, 0, ',', '.') }}
                                </td>

                                <td>
                                    {{ number_format($bahan->stok_minimum, 0, ',', '.') }}

                                <td>{{ $bahan->satuan_dasar }}</td>

                                <td>
                                    <span class="badge bg-danger">
                                        Menipis
                                    </span>
                                </td>
                            </tr>
                        @empty

                                <td colspan="6" class="text-center text-muted py-4">
                                    Semua stok bahan masih mencukupi.
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

@extends('manager.layouts.app')
@section('title', 'Laporan Margin - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-2">Laporan Margin per Menu</h3>
    <p class="text-muted mb-4">
        Menampilkan harga jual, HPP, dan margin keuntungan setiap menu.
    </p>

    {{-- Tabel laporan margin --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Harga Jual</th>
                            <th>HPP</th>
                            <th>Margin</th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- Memeriksa apakah data menu tersedia --}}
                        @forelse ($laporanMargin as $index => $item)

                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['nama_menu'] }}</td>
                                <td>Rp{{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item['hpp'], 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">
                                    Rp{{ number_format($item['margin'], 0, ',', '.') }}
                                </td>
                            </tr>

                        @empty

                            {{-- Pesan jika belum ada menu --}}
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
    </div>

</div>

@endsection

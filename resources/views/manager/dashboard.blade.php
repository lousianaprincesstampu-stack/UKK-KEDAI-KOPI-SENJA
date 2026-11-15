
@extends('manager.layouts.app')

@section('title', 'Dashboard Manager - Kedai Kopi Senja')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold">Dashboard Manager</h3>

        <p class="text-muted">
            Selamat datang, {{ Auth::user()->name }}!
        </p>
    </div>

    <div class="row g-3 mb-4">

        <!-- Total Menu -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Menu</p>
                        <h3 class="fw-bold mb-0">{{ $totalMenu }}</h3>
                    </div>

                    <i class="bi bi-journal-text card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Total Bahan -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Bahan</p>
                        <h3 class="fw-bold mb-0">{{ $totalBahan }}</h3>
                    </div>

                    <i class="bi bi-box-seam card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Penjualan Hari Ini</p>

                        <h3 class="fw-bold mb-0">
                            Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
                        </h3>
                    </div>

                    <i class="bi bi-cash-stack card-icon"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Stok Bahan Menipis -->
    <div class="table-container">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Stok Bahan Menipis</h5>

            <span class="badge text-bg-warning">Perlu Diperhatikan</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Bahan</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Minimum</th>
                        <th>Status</th>
                    </tr>
                </thead>

                
                    <tbody>
                        {{-- Menampilkan daftar bahan yang stoknya menipis --}}
                        @forelse ($bahanMenipis as $bahan)
                            <tr>
                                {{-- Nomor urut --}}
                                <td>{{ $loop->iteration }}</td>

                                {{-- Nama bahan --}}
                                <td>{{ $bahan->nama_bahan }}</td>

                                {{-- Stok saat ini beserta satuannya --}}
                                <td>
                                    {{ rtrim(rtrim(number_format((float) $bahan->stok, 3, ',', '.'), '0'), ',') }}
                                    {{ $bahan->satuan_dasar }}
                                </td>

                                {{-- Stok minimum beserta satuannya --}}
                                <td>
                                    {{ rtrim(rtrim(number_format((float) $bahan->stok_minimum, 3, ',', '.'), '0'), ',') }}
                                    {{ $bahan->satuan_dasar }}
                                </td>

                                {{-- Status stok --}}
                                <td>
                                    <span class="badge text-bg-danger">Menipis</span>
                                </td>
                            </tr>

                        @empty
                            {{-- Ditampilkan jika tidak ada bahan yang stoknya menipis --}}
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tidak ada bahan dengan stok menipis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>


            </table>
        </div>

    </div>

@endsection

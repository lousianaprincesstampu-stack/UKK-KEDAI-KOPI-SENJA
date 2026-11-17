@extends('cashier.layouts.app')
@section('title', 'Dashboard Kasir - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Dashboard Kasir</h3>
        <p class="text-muted">
            Selamat datang, {{ Auth::user()->name }}.
        </p>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="text-muted">Pesanan Baru</h6>
                    <h3 class="fw-bold">{{ $pesananBaru }} Pesanan</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="text-muted">Diproses</h6>
                    <h3 class="fw-bold">{{ $pesananDiproses }} Pesanan</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="text-muted">Selesai Hari Ini</h6>
                    <h3 class="fw-bold">{{ $pesananSelesai }} Pesanan</h3>
                </div>
            </div>
        </div>

    </div>

    <div class="table-container">

        <div class="mb-3">
            <h5 class="fw-bold mb-1">Pesanan Terbaru</h5>
            <p class="text-muted mb-0">
                Daftar pesanan pelanggan yang masuk.
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pesananTerbaru as $pesanan)
                        <tr>
                            <td>{{ $pesanan->kode_pesanan }}</td>

                            <td>
                                {{ $pesanan->customer->nama ?? '-' }}
                            </td>

                            <td>
                                {{$pesanan->cafeTable->nomor_meja ?? '-' }}
                            </td>

                            <td>
                                {{ $pesanan->tanggal_pesanan?->format('H:i') ?? '-' }}
                            </td>

                            <td>
                                Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $pesanan->status_pesanan }}
                                </span>
                            </td>

                            <td>
                                <span class="text-muted">-</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada pesanan.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection
